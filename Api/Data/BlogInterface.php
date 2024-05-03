<?php
/**
 * Copyright © 2023, Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Space\Blog\Api\Data;

interface BlogInterface
{
    /**
     * Constants for keys of data array
     */
    public const BLOG_ID = 'blog_id';
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
     * @return BlogInterface
     */
    public function setId(int $id): BlogInterface;

    /**
     * Set title
     *
     * @param string $title
     * @return BlogInterface
     */
    public function setTitle(string $title): BlogInterface;

    /**
     * Set content
     *
     * @param string $content
     * @return BlogInterface
     */
    public function setContent(string $content): BlogInterface;

    /**
     * Set author
     *
     * @param string $author
     * @return BlogInterface
     */
    public function setAuthor(string $author): BlogInterface;

    /**
     * Set creation time
     *
     * @param string $creationTime
     * @return BlogInterface
     */
    public function setCreationTime(string $creationTime): BlogInterface;

    /**
     * Set update time
     *
     * @param string $updateTime
     * @return BlogInterface
     */
    public function setUpdateTime(string $updateTime): BlogInterface;

    /**
     * Set is active
     *
     * @param bool|int $isActive
     * @return BlogInterface
     */
    public function setIsActive(bool|int $isActive): BlogInterface;
}
