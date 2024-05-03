<?php
/**
 * Copyright © 2023, Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Space\Blog\Model\ResourceModel\Blog\Stores;

use Space\Blog\Model\ResourceModel\Blog;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\Exception\LocalizedException;

class ReadHandler implements ExtensionInterface
{
    /**
     * @var Blog
     */
    protected Blog $resourceBlog;

    /**
     * Constructor
     *
     * @param Blog $resourceBlog
     */
    public function __construct(
        Blog $resourceBlog
    ) {
        $this->resourceBlog = $resourceBlog;
    }

    /**
     * Execute
     *
     * @param object $entity
     * @param array $arguments
     * @return object
     * @throws LocalizedException
     */
    public function execute($entity, $arguments = []): object
    {
        if ($entity->getId()) {
            $stores = $this->resourceBlog->lookupStoreIds((int)$entity->getId());
            $entity->setData('store_id', $stores);
            $entity->setData('stores', $stores);
        }
        return $entity;
    }
}
