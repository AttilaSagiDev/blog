<?php
/**
 * Copyright © 2023, Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Space\Blog\Model;

use Magento\Framework\Api\SearchResults;
use Space\Blog\Api\Data\BlogSearchResultsInterface;

class BlogSearchResults extends SearchResults implements BlogSearchResultsInterface
{
}
