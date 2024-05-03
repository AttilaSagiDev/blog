<?php
/**
 * Copyright © 2023, Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Space\Blog\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface BlogSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get blog items
     *
     * @return BlogInterface[]
     */
    public function getItems();

    /**
     * Set blog items
     *
     * @param BlogInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
