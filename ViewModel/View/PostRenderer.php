<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\Blog\ViewModel\View;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\App\RequestInterface;
use Space\Blog\Api\PostRepositoryInterface;
use Magento\Framework\UrlInterface;
use Space\Blog\Api\Data\PostInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class PostRenderer implements ArgumentInterface
{
    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var PostRepositoryInterface
     */
    private PostRepositoryInterface $postRepository;

    /**
     * @var UrlInterface
     */
    private UrlInterface $urlInterface;

    /**
     * Constructor
     *
     * @param RequestInterface $request
     * @param PostRepositoryInterface $postRepository
     * @param UrlInterface $urlInterface
     */
    public function __construct(
        RequestInterface $request,
        PostRepositoryInterface $postRepository,
        UrlInterface $urlInterface
    ) {
        $this->request = $request;
        $this->postRepository = $postRepository;
        $this->urlInterface = $urlInterface;
    }

    /**
     * Get post
     *
     * @return PostInterface|null
     */
    public function getPost(): ?PostInterface
    {
        $postId = (int)$this->request->getParam('id');
        if ($postId) {
            try {
                return $this->postRepository->getById($postId);
            } catch (NoSuchEntityException|LocalizedException $e) {
                return null;
            }
        }

        return null;
    }

    /**
     * Get back url
     *
     * @return string
     */
    public function getBackUrl(): string
    {
        return $this->urlInterface->getUrl('blog/');
    }
}
