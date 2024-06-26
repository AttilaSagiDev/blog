<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\Blog\Controller\Adminhtml\Blog;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Space\Blog\Model\Post;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Backend\Model\View\Result\Page;

class Edit extends Action implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    public const ADMIN_RESOURCE = 'Space_Blog::blog';

    /**
     * @var Registry
     */
    private Registry $registry;

    /**
     * @var PageFactory
     */
    private PageFactory $resultPageFactory;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        PageFactory $resultPageFactory
    ) {
        $this->registry = $registry;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Edit post
     *
     * @return Page|Redirect|ResponseInterface|ResultInterface
     */
    public function execute(): Redirect|ResultInterface|ResponseInterface|Page
    {
        $id = $this->getRequest()->getParam('post_id');
        $post = $this->_objectManager->create(Post::class);

        if ($id) {
            $post->load($id);
            if (!$post->getId()) {
                $this->messageManager->addErrorMessage(__('This post no longer exists.'));
                /** @var Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $this->registry->register('blog_post', $post);

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Space_Blog::blog')
            ->addBreadcrumb(__('Edit Post'), __('Edit Post')) //NOSONAR
            ->addBreadcrumb(__('Edit Post'), __('Edit Post')); //NOSONAR
        $resultPage->getConfig()->getTitle()->prepend(__('Posts'));
        $resultPage->getConfig()->getTitle()->prepend($post->getId() ? $post->getTitle() : __('New Post'));

        return $resultPage;
    }
}
