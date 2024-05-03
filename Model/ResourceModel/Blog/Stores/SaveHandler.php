<?php
/**
 * Copyright © 2023, Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Space\Blog\Model\ResourceModel\Blog\Stores;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Space\Blog\Api\Data\BlogInterface;
use Space\Blog\Model\ResourceModel\Blog;
use Exception;

class SaveHandler implements ExtensionInterface
{
    /**
     * @var MetadataPool
     */
    protected MetadataPool $metadataPool;

    /**
     * @var Blog
     */
    protected Blog $resourceBlog;

    /**
     * @param MetadataPool $metadataPool
     * @param Blog $resourceBlog
     */
    public function __construct(
        MetadataPool $metadataPool,
        Blog $resourceBlog
    ) {
        $this->metadataPool = $metadataPool;
        $this->resourceBlog = $resourceBlog;
    }

    /**
     * Execute
     *
     * @param object $entity
     * @param array $arguments
     * @return object
     * @throws Exception
     */
    public function execute($entity, $arguments = []): object
    {
        $entityMetadata = $this->metadataPool->getMetadata(BlogInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $connection = $entityMetadata->getEntityConnection();

        $oldStores = $this->resourceBlog->lookupStoreIds((int)$entity->getId());
        $newStores = (array)$entity->getStoreId();

        $table = $this->resourceBlog->getTable('space_blog_store');

        $delete = array_diff($oldStores, $newStores);
        if ($delete) {
            $where = [
                $linkField . ' = ?' => (int)$entity->getData($linkField),
                'store_id IN (?)' => $delete,
            ];
            $connection->delete($table, $where);
        }

        $insert = array_diff($newStores, $oldStores);
        if ($insert) {
            $data = [];
            foreach ($insert as $storeId) {
                $data[] = [
                    $linkField => (int)$entity->getData($linkField),
                    'store_id' => (int)$storeId,
                ];
            }
            $connection->insertMultiple($table, $data);
        }

        return $entity;
    }
}
