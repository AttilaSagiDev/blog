<?php
/**
 * Copyright © 2023, Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Space\Blog\Block\View;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Space\Blog\Api\BlogRepositoryInterface;
use Space\Blog\Api\Data\BlogInterface;

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
     * @return BlogInterface|void
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
        return (int)$this->getData(BlogInterface::BLOG_ID);
    }
}
