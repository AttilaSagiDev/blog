<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\Blog\Controller\Post;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use Space\Blog\Api\PostRepositoryInterface;
use Space\Blog\Api\Data\ConfigInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Space\Blog\Block\View\Post;
use Space\Blog\Api\Data\PostInterface;

class View extends Action implements HttpGetActionInterface
{
    /**
     * @var PageFactory
     */
    private PageFactory $resultPageFactory;

    /**
     * @var PostRepositoryInterface
     */
    private PostRepositoryInterface $postRepository;

    /**
     * @var ConfigInterface
     */
    private ConfigInterface $config;

    /**
     * Constructor
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param PostRepositoryInterface $postRepository
     * @param ConfigInterface $config
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        PostRepositoryInterface $postRepository,
        ConfigInterface $config
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->postRepository = $postRepository;
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
        $postId = (int)$this->getRequest()->getParam('id');
        if (!$postId || !$this->config->isEnabled()) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $this->messageManager->addErrorMessage('Invalid post ID or module disabled.');
            return $resultRedirect->setPath('no-route');
        }

        $resultPage = $this->resultPageFactory->create();
        try {
            $post = $this->postRepository->getById($postId);
            /** @var Post $viewBlock */
            $viewBlock = $resultPage->getLayout()->getBlock('space.blog.view');
            $viewBlock->setData('post', $post);
            $resultPage->getConfig()->getTitle()->set($post->getTitle() ? $post->getTitle() : __('Post'));
        } catch (NoSuchEntityException|LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while viewing the post.'));
        }

        return $resultPage;
    }
}
