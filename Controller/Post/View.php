<?php
/**
 * Copyright © 2023, Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Space\Blog\Controller\Post;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Space\Blog\Api\Data\ConfigInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Space\Blog\Block\View\Post;
use Space\Blog\Api\Data\BlogInterface;

class View extends Action implements HttpGetActionInterface
{
    /**
     * @var PageFactory
     */
    private PageFactory $resultPageFactory;

    /**
     * @var ConfigInterface
     */
    private ConfigInterface $config;

    /**
     * Constructor
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param ConfigInterface $config
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ConfigInterface $config
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->config = $config;
        parent::__construct($context);
    }

    /**
     * View action
     *
     * @return ResultInterface|ResponseInterface|Redirect
     */
    public function execute(): ResultInterface|ResponseInterface|Redirect
    {
        $postId = $this->getRequest()->getParam('id');
        if (!$postId || !$this->config->isEnabled()) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $this->messageManager->addErrorMessage('Invalid post ID or module disabled.');
            return $resultRedirect->setPath('no-route');
        }

        $resultPage = $this->resultPageFactory->create();
        /** @var Post $viewBlock */
        $viewBlock = $resultPage->getLayout()->getBlock('space.blog.view');
        $viewBlock?->setData(BlogInterface::BLOG_ID, (int)$postId);

        return $resultPage;
    }
}
