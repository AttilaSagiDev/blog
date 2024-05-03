<?php
/**
 * Copyright © 2023, Open Software License ("OSL") v. 3.0
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
use Space\Blog\Model\Blog;
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
        $id = $this->getRequest()->getParam('blog_id');
        $blog = $this->_objectManager->create(Blog::class);

        if ($id) {
            $blog->load($id);
            if (!$blog->getId()) {
                $this->messageManager->addErrorMessage(__('This post no longer exists.'));
                /** @var Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $this->registry->register('space_blog', $blog);

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Space_Blog::blog')
            ->addBreadcrumb(__('Edit Post'), __('Edit Post'))
            ->addBreadcrumb(__('Edit Post'), __('Edit Post'));
        $resultPage->getConfig()->getTitle()->prepend(__('Posts'));
        $resultPage->getConfig()->getTitle()->prepend($blog->getId() ? $blog->getTitle() : __('New Post'));

        return $resultPage;
    }
}
