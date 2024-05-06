<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\Blog\Api\Data;

interface PostInterface
{
    /**
     * Constants for keys of data array
     */
    public const POST_ID = 'post_id';
    public const TITLE = 'title';
    public const CONTENT = 'content';
    public const AUTHOR = 'author';
    public const CREATION_TIME = 'creation_time';
    public const UPDATE_TIME = 'update_time';
    public const IS_ACTIVE = 'is_active';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * Get title
     *
     * @return string|null
     */
    public function getTitle(): ?string;

    /**
     * Get content
     *
     * @return string|null
     */
    public function getContent(): ?string;

    /**
     * Get author
     *
     * @return string|null
     */
    public function getAuthor(): ?string;

    /**
     * Get creation time
     *
     * @return string|null
     */
    public function getCreationTime(): ?string;

    /**
     * Get update time
     *
     * @return string|null
     */
    public function getUpdateTime(): ?string;

    /**
     * Is active
     *
     * @return bool|null
     */
    public function isActive(): ?bool;

    /**
     * Set ID
     *
     * @param int $id
     * @return PostInterface
     */
    public function setId(int $id): PostInterface;

    /**
     * Set title
     *
     * @param string $title
     * @return PostInterface
     */
    public function setTitle(string $title): PostInterface;

    /**
     * Set content
     *
     * @param string $content
     * @return PostInterface
     */
    public function setContent(string $content): PostInterface;

    /**
     * Set author
     *
     * @param string $author
     * @return PostInterface
     */
    public function setAuthor(string $author): PostInterface;

    /**
     * Set creation time
     *
     * @param string $creationTime
     * @return PostInterface
     */
    public function setCreationTime(string $creationTime): PostInterface;

    /**
     * Set update time
     *
     * @param string $updateTime
     * @return PostInterface
     */
    public function setUpdateTime(string $updateTime): PostInterface;

    /**
     * Set is active
     *
     * @param bool|int $isActive
     * @return PostInterface
     */
    public function setIsActive(bool|int $isActive): PostInterface;
}
