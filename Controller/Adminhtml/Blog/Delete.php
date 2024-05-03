<?php
/**
 * Copyright © 2023, Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Space\Blog\Controller\Adminhtml\Blog;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Space\Blog\Model\BlogRepository;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Delete extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    public const ADMIN_RESOURCE = 'Space_Blog::blog';

    /**
     * @var BlogRepository
     */
    private BlogRepository $blogRepository;

    /**
     * Constructor
     *
     * @param Context $context
     * @param BlogRepository $blogRepository
     */
    public function __construct(
        Context $context,
        BlogRepository $blogRepository
    ) {
        $this->blogRepository = $blogRepository;
        parent::__construct($context);
    }

    /**
     * Delete action
     *
     * @return ResultInterface|ResponseInterface|Redirect
     */
    public function execute(): ResultInterface|ResponseInterface|Redirect
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = (int)$this->getRequest()->getParam('blog_id');
        if ($id) {
            try {
                $blog = $this->blogRepository->getById($id);
                if ($blog->getId()) {
                    $this->blogRepository->deleteById($blog->getId());
                }
                $this->messageManager->addSuccessMessage(__('You deleted the post.'));
                return $resultRedirect->setPath('*/*/');
            } catch (NoSuchEntityException|\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['blog_id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a post to delete.'));

        return $resultRedirect->setPath('*/*/');
    }
}
