<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\Blog\Model;

use Magento\Framework\Api\SearchResults;
use Space\Blog\Api\Data\PostSearchResultsInterface;

class PostSearchResults extends SearchResults implements PostSearchResultsInterface
{
}
