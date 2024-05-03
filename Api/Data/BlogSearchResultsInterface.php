<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\Blog\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface BlogSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get blog items
     *
     * @return PostInterface[]
     */
    public function getItems();

    /**
     * Set blog items
     *
     * @param PostInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
