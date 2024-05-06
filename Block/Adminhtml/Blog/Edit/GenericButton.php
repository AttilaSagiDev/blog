<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\Blog\Block\Adminhtml\Blog\Edit;

use Magento\Backend\Block\Widget\Context;
use Space\Blog\Api\PostRepositoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;

class GenericButton
{
    /**
     * @var Context
     */
    protected Context $context;

    /**
     * @var PostRepositoryInterface
     */
    protected PostRepositoryInterface $postRepository;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * Construct
     *
     * @param Context $context
     * @param PostRepositoryInterface $postRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        PostRepositoryInterface $postRepository,
        LoggerInterface $logger
    ) {
        $this->context = $context;
        $this->postRepository = $postRepository;
        $this->logger = $logger;
    }

    /**
     * Return blog ID
     *
     * @return int|null
     * @throws LocalizedException
     */
    public function getPostId(): ?int
    {
        try {
            return $this->postRepository->getById(
                (int)$this->context->getRequest()->getParam('post_id')
            )->getId();
        } catch (NoSuchEntityException $e) {
            $this->logger->error($e->getMessage());
        }

        return null;
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl(string $route = '', array $params = []): string
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
