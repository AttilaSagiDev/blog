<?php
/**
 * Copyright © 2023, Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Space\Blog\Block\Adminhtml\Blog\Edit;

use Magento\Backend\Block\Widget\Context;
use Space\Blog\Api\BlogRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;

class GenericButton
{
    /**
     * @var Context
     */
    protected Context $context;

    /**
     * @var BlogRepositoryInterface
     */
    protected BlogRepositoryInterface $blogRepository;

    /**
     * Construct
     *
     * @param Context $context
     * @param BlogRepositoryInterface $blogRepository
     */
    public function __construct(
        Context $context,
        BlogRepositoryInterface $blogRepository
    ) {
        $this->context = $context;
        $this->blogRepository = $blogRepository;
    }

    /**
     * Return blog ID
     *
     * @return int|null
     * @throws LocalizedException
     */
    public function getBlogId(): ?int
    {
        try {
            return $this->blogRepository->getById(
                (int)$this->context->getRequest()->getParam('blog_id')
            )->getId();
        } catch (NoSuchEntityException $e) {
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
