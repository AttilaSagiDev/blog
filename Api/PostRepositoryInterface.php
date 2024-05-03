<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\Blog\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Space\Blog\Api\Data\PostSearchResultsInterface;
use Magento\Framework\Exception\LocalizedException;
use Space\Blog\Api\Data\PostInterface;
use Magento\Framework\Exception\NoSuchEntityException;

interface PostRepositoryInterface
{
    /**
     * Retrieve blogs matching the specified criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return PostSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria): PostSearchResultsInterface;

    /**
     * Retrieve blog
     *
     * @param int $blogId
     * @return PostInterface
     * @throws LocalizedException
     */
    public function getById(int $blogId): PostInterface;

    /**
     * Save blog
     *
     * @param PostInterface $blog
     * @return PostInterface
     * @throws LocalizedException
     */
    public function save(PostInterface $blog): PostInterface;

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
     * @param PostInterface $blog
     * @return bool
     * @throws LocalizedException
     */
    public function delete(PostInterface $blog): bool;
}
