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
     * @return \Space\Blog\Api\Data\PostSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria): PostSearchResultsInterface;

    /**
     * Retrieve post
     *
     * @param int $postId
     * @return \Space\Blog\Api\Data\PostInterface
     * @throws LocalizedException
     */
    public function getById(int $postId): PostInterface;

    /**
     * Save post
     *
     * @param PostInterface $post
     * @return PostInterface
     * @throws LocalizedException
     */
    public function save(PostInterface $post): PostInterface;

    /**
     * Delete post by ID
     *
     * @param int $postId
     * @return bool
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById(int $postId): bool;

    /**
     * Delete post
     *
     * @param PostInterface $post
     * @return bool
     * @throws LocalizedException
     */
    public function delete(PostInterface $post): bool;
}
