<?php
/**
 * Copyright © 2023, Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Space\Blog\Model\ResourceModel\Post\Stores;

use Space\Blog\Model\ResourceModel\Post;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\Exception\LocalizedException;

class ReadHandler implements ExtensionInterface
{
    /**
     * @var Post
     */
    protected Post $resourcePost;

    /**
     * Constructor
     *
     * @param Post $resourcePost
     */
    public function __construct(
        Post $resourcePost
    ) {
        $this->resourcePost = $resourcePost;
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
            $stores = $this->resourcePost->lookupStoreIds((int)$entity->getId());
            $entity->setData('store_id', $stores);
            $entity->setData('stores', $stores);
        }
        return $entity;
    }
}
