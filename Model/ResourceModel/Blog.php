<?php
/**
 * Copyright © 2023, Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Space\Blog\Model\ResourceModel;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Space\Blog\Api\Data\BlogInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DB\Select;
use Space\Blog\Model\Blog as BlogModel;
use Magento\Store\Model\Store;
use Magento\Framework\Exception\LocalizedException;
use Exception;

class Blog extends AbstractDb
{
    /**
     * @var EntityManager
     */
    protected EntityManager $entityManager;

    /**
     * @var MetadataPool
     */
    protected MetadataPool $metadataPool;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * Constructor
     *
     * @param Context $context
     * @param EntityManager $entityManager
     * @param MetadataPool $metadataPool
     * @param StoreManagerInterface $storeManager
     * @param string|null $connectionName
     */
    public function __construct(
        Context $context,
        EntityManager $entityManager,
        MetadataPool $metadataPool,
        StoreManagerInterface $storeManager,
        string $connectionName = null
    ) {
        $this->entityManager = $entityManager;
        $this->metadataPool = $metadataPool;
        $this->storeManager = $storeManager;
        parent::__construct($context, $connectionName);
    }

    /**
     * Construct with init
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init('space_blog', BlogInterface::BLOG_ID);
    }

    /**
     * Get connection
     *
     * @throws Exception
     */
    public function getConnection(): AdapterInterface|bool
    {
        return $this->metadataPool->getMetadata(BlogInterface::class)->getEntityConnection();
    }

    /**
     * Load an object
     *
     * @param AbstractModel $object
     * @param mixed $value
     * @param string $field
     * @return $this|Blog
     * @throws LocalizedException
     */
    public function load(AbstractModel $object, $value, $field = null): Blog|static
    {
        $blogId = $this->getBlogId($object, $value, $field);
        if ($blogId) {
            $this->entityManager->load($object, $blogId);
        }

        return $this;
    }

    /**
     * Save an object
     *
     * @param AbstractModel $object
     * @return $this
     * @throws Exception
     */
    public function save(AbstractModel $object): static
    {
        $this->entityManager->save($object);

        return $this;
    }

    /**
     * Delete the object
     *
     * @param AbstractModel $object
     * @return AbstractDb|Blog|$this
     * @throws Exception
     */
    public function delete(AbstractModel $object): AbstractDb|Blog|static
    {
        $this->entityManager->delete($object);

        return $this;
    }

    /**
     * Get Blog ID
     *
     * @param AbstractModel $object
     * @param mixed $value
     * @param string|null $field
     * @return bool|int|string
     * @throws LocalizedException
     * @throws Exception
     */
    private function getBlogId(AbstractModel $object, mixed $value, string $field = null): bool|int|string
    {
        $entityMetadata = $this->metadataPool->getMetadata(BlogInterface::class);
        if (!$field) {
            $field = $entityMetadata->getIdentifierField();
        }
        $entityId = $value;
        if ($field != $entityMetadata->getIdentifierField() || $object->getStoreId()) {
            $select = $this->_getLoadSelect($field, $value, $object);
            $select->reset(Select::COLUMNS)
                ->columns($this->getMainTable() . '.' . $entityMetadata->getIdentifierField())
                ->limit(1);
            $result = $this->getConnection()->fetchCol($select);
            $entityId = count($result) ? $result[0] : false;
        }

        return $entityId;
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param BlogModel|AbstractModel $object
     * @return Select
     * @throws Exception
     */
    protected function _getLoadSelect($field, $value, $object): Select
    {
        $entityMetadata = $this->metadataPool->getMetadata(BlogInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $stores = [(int)$object->getStoreId(), Store::DEFAULT_STORE_ID];

            $select->join(
                ['sbs' => $this->getTable('space_blog_store')],
                $this->getMainTable() . '.' . $linkField . ' = sbs.' . $linkField,
                ['store_id']
            )
                ->where('is_active = ?', 1)
                ->where('sbs.store_id in (?)', $stores)
                ->order('store_id DESC')
                ->limit(1);
        }

        return $select;
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     * @throws LocalizedException
     * @throws Exception
     */
    public function lookupStoreIds(int $id): array
    {
        $connection = $this->getConnection();

        $entityMetadata = $this->metadataPool->getMetadata(BlogInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = $connection->select()
            ->from(['sbs' => $this->getTable('space_blog_store')], 'store_id')
            ->join(
                ['sb' => $this->getMainTable()],
                'sbs.' . $linkField . ' = sb.' . $linkField,
                []
            )
            ->where('sb.' . $entityMetadata->getIdentifierField() . ' = :blog_id');

        return $connection->fetchCol($select, ['blog_id' => (int)$id]);
    }
}
