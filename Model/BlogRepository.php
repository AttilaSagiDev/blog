<?php
/**
 * Copyright © 2023, Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Space\Blog\Model;

use Space\Blog\Api\BlogRepositoryInterface;
use Space\Blog\Api\Data;
use Space\Blog\Api\Data\BlogInterface;
use Space\Blog\Model\ResourceModel\Blog as ResourceBlog;
use Space\Blog\Model\ResourceModel\Blog\CollectionFactory as BlogCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;
use Space\Blog\Api\Data\BlogInterfaceFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;

class BlogRepository implements BlogRepositoryInterface
{
    /**
     * @var ResourceBlog
     */
    protected ResourceBlog $resource;

    /**
     * @var BlogFactory
     */
    protected BlogFactory $blogFactory;

    /**
     * @var BlogCollectionFactory
     */
    protected BlogCollectionFactory $blogCollectionFactory;

    /**
     * @var Data\BlogSearchResultsInterfaceFactory
     */
    protected Data\BlogSearchResultsInterfaceFactory $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    protected DataObjectHelper $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected DataObjectProcessor $dataObjectProcessor;

    /**
     * @var BlogInterfaceFactory
     */
    protected BlogInterfaceFactory $dataBlogFactory;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var CollectionProcessorInterface
     */
    private CollectionProcessorInterface $collectionProcessor;

    /**
     * @var HydratorInterface
     */
    private HydratorInterface $hydrator;

    /**
     * @param ResourceBlog $resource
     * @param BlogFactory $blogFactory
     * @param BlogInterfaceFactory $dataBlogFactory
     * @param BlogCollectionFactory $blogCollectionFactory
     * @param Data\BlogSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface|null $collectionProcessor
     * @param HydratorInterface|null $hydrator
     */
    public function __construct(
        ResourceBlog $resource,
        BlogFactory $blogFactory,
        BlogInterfaceFactory $dataBlogFactory,
        BlogCollectionFactory $blogCollectionFactory,
        Data\BlogSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor = null,
        ?HydratorInterface $hydrator = null
    ) {
        $this->resource = $resource;
        $this->blogFactory = $blogFactory;
        $this->blogCollectionFactory = $blogCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataBlogFactory = $dataBlogFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor ?:
            ObjectManager::getInstance()->get(CollectionProcessorInterface::class);
        $this->hydrator = $hydrator ?? ObjectManager::getInstance()->get(HydratorInterface::class);
    }

    /**
     * Save
     *
     * @param BlogInterface $blog
     * @return BlogInterface
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException|LocalizedException
     */
    public function save(BlogInterface $blog): BlogInterface
    {
        if (empty($blog->getStoreId())) {
            $blog->setStoreId($this->storeManager->getStore()->getId());
        }

        if ($blog->getId() && $blog instanceof Blog && !$blog->getOrigData()) {
            $blog = $this->hydrator->hydrate($this->getById($blog->getId()), $this->hydrator->extract($blog));
        }

        try {
            $this->resource->save($blog);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $blog;
    }

    /**
     * Retrieve blog
     *
     * @param int $blogId
     * @return BlogInterface
     * @throws NoSuchEntityException|LocalizedException
     */
    public function getById(int $blogId): BlogInterface
    {
        $blog = $this->blogFactory->create();
        $this->resource->load($blog, $blogId);
        if (!$blog->getId()) {
            throw new NoSuchEntityException(__('The post with the "%1" ID doesn\'t exist.', $blogId));
        }

        return $blog;
    }

    /**
     * Retrieve blogs matching the specified criteria
     *
     * @param SearchCriteriaInterface $criteria
     * @return Data\BlogSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $criteria): Data\BlogSearchResultsInterface
    {
        $collection = $this->blogCollectionFactory->create();
        $this->collectionProcessor->process($criteria, $collection);

        /** @var Data\BlogSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * Delete blog
     *
     * @param BlogInterface $blog
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(BlogInterface $blog): bool
    {
        try {
            $this->resource->delete($blog);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }

        return true;
    }

    /**
     * Delete by ID
     *
     * @param int $blogId
     * @return bool
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById(int $blogId): bool
    {
        return $this->delete($this->getById($blogId));
    }
}
