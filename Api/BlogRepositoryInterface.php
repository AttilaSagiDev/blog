<?php
/**
 * Copyright © 2023, Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Space\Blog\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Space\Blog\Api\Data\BlogSearchResultsInterface;
use Magento\Framework\Exception\LocalizedException;
use Space\Blog\Api\Data\BlogInterface;
use Magento\Framework\Exception\NoSuchEntityException;

interface BlogRepositoryInterface
{
    /**
     * Retrieve blogs matching the specified criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return BlogSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria): BlogSearchResultsInterface;

    /**
     * Retrieve blog
     *
     * @param int $blogId
     * @return BlogInterface
     * @throws LocalizedException
     */
    public function getById(int $blogId): BlogInterface;

    /**
     * Save blog
     *
     * @param BlogInterface $blog
     * @return BlogInterface
     * @throws LocalizedException
     */
    public function save(BlogInterface $blog): BlogInterface;

    /**
     * Delete blog by ID
     *
     * @param int $blogId
     * @return bool
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById(int $blogId): bool;

    /**
     * Delete blog
     *
     * @param BlogInterface $blog
     * @return bool
     * @throws LocalizedException
     */
    public function delete(BlogInterface $blog): bool;
}
