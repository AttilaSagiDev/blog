<?php
/**
 * Copyright © 2023, Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Space\Blog\Model\Api\SearchCriteria\CollectionProcessor\FilterProcessor;

use Magento\Framework\Api\SearchCriteria\CollectionProcessor\FilterProcessor\CustomFilterInterface;
use Magento\Framework\Api\Filter;
use Magento\Framework\Data\Collection\AbstractDb;
use Space\Blog\Model\ResourceModel\Blog\Collection;

class BlogStoreFilter implements CustomFilterInterface
{
    /**
     * Apply custom store filter to collection
     *
     * @param Filter $filter
     * @param AbstractDb $collection
     * @return bool
     */
    public function apply(Filter $filter, AbstractDb $collection): bool
    {
        /** @var Collection $collection */
        $collection->addStoreFilter($filter->getValue(), false);

        return true;
    }
}
