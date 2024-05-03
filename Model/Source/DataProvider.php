<?php
/**
 * Copyright © 2023, Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Space\Blog\Model\Source;

use Magento\Ui\DataProvider\ModifierPoolDataProvider;
use Space\Blog\Model\ResourceModel\Blog\CollectionFactory;
use Space\Blog\Model\ResourceModel\Blog\Collection;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Space\Blog\Model\Blog;

class DataProvider extends ModifierPoolDataProvider
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected DataPersistorInterface $dataPersistor;

    /**
     * @var array|null
     */
    protected ?array $loadedData = null;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $blogCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     * @param PoolInterface|null $pool
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $blogCollectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = [],
        PoolInterface $pool = null
    ) {
        $this->collection = $blogCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data, $pool);
    }

    /**
     * Get data
     *
     * @return null|array
     */
    public function getData(): ?array
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var Blog $blog */
        foreach ($items as $blog) {
            $this->loadedData[$blog->getId()] = $blog->getData();
        }

        $data = $this->dataPersistor->get('space_blog');
        if (!empty($data)) {
            $blog = $this->collection->getNewEmptyItem();
            $blog->setData($data);
            $this->loadedData[$blog->getId()] = $blog->getData();
            $this->dataPersistor->clear('space_blog');
        }

        return $this->loadedData;
    }
}
