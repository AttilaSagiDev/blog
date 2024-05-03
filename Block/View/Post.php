<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\Blog\Block\View;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Space\Blog\Api\BlogRepositoryInterface;
use Space\Blog\Api\Data\PostInterface;

class Post extends Template
{
    /**
     * @var BlogRepositoryInterface
     */
    private BlogRepositoryInterface $blogRepository;

    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param BlogRepositoryInterface $blogRepository
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        BlogRepositoryInterface $blogRepository,
        array $data = []
    ) {
        $this->blogRepository = $blogRepository;
        parent::__construct($context, $data);
    }

    /**
     * Get post
     *
     * @return PostInterface|void
     * @throws LocalizedException
     */
    public function getPost()
    {
        $postId = $this->getPostId();
        if ($postId) {
            return $this->blogRepository->getById($postId);
        }
    }

    /**
     * Get back url
     *
     * @return string
     */
    public function getBackUrl(): string
    {
        return $this->getUrl('blog/');
    }

    /**
     * Get post ID
     *
     * @return int
     */
    private function getPostId(): int
    {
        return (int)$this->getData(PostInterface::BLOG_ID);
    }
}
