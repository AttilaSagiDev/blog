<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\Blog\Block\View;

use Magento\Framework\View\Element\Template;
use Space\Blog\Api\Data\PostInterface;

class Post extends Template
{
    /**
     * @var PostInterface|null
     */
    private ?PostInterface $post = null;

    /**
     * Get post
     *
     * @return PostInterface|null
     */
    public function getPost(): ?PostInterface
    {
        if ($this->post === null) {
            $this->post = $this->getData('post');
        }

        return $this->post;
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
}
