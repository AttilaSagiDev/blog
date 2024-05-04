<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\Blog\Controller\Adminhtml\Blog;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Space\Blog\Model\PostFactory;
use Space\Blog\Api\PostRepositoryInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Backend\Model\View\Result\Redirect;
use Space\Blog\Model\Source\IsActive;
use Space\Blog\Model\Post;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\App\ResponseInterface;

class Save extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    public const ADMIN_RESOURCE = 'Space_Blog::blog';

    /**
     * @var DataPersistorInterface
     */
    protected DataPersistorInterface $dataPersistor;

    /**
     * @var PostFactory
     */
    private PostFactory $postFactory;

    /**
     * @var PostRepositoryInterface
     */
    private PostRepositoryInterface $postRepository;

    /**
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param PostFactory|null $postFactory
     * @param PostRepositoryInterface|null $postRepository
     */
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        PostFactory $postFactory = null,
        PostRepositoryInterface $postRepository = null
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->postFactory = $postFactory
            ?: ObjectManager::getInstance()->get(PostFactory::class);
        $this->postRepository = $postRepository
            ?: ObjectManager::getInstance()->get(PostRepositoryInterface::class);
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return Redirect|ResponseInterface|ResultInterface
     */
    public function execute(): Redirect|ResultInterface|ResponseInterface
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        if ($data) {
            if (isset($data['is_active']) && $data['is_active'] === 'true') {
                $data['is_active'] = IsActive::STATUS_ENABLED;
            }
            if (empty($data['post_id'])) {
                $data['post_id'] = null;
            }

            /** @var Post $model */
            $model = $this->postFactory->create();

            $id = (int)$this->getRequest()->getParam('post_id');
            if ($id) {
                try {
                    $model = $this->postRepository->getById($id);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage(__('This post no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }

            $model->setData($data);

            try {
                $this->postRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the post.'));
                $this->dataPersistor->clear('blog_post');
                return $this->processBlogReturn($model, $data, $resultRedirect);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the post.'));
            }

            $this->dataPersistor->set('blog_post', $data);
            return $resultRedirect->setPath('*/*/edit', ['post_id' => $id]);
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Process and set the post return
     *
     * @param Post $model
     * @param array $data
     * @param ResultInterface $resultRedirect
     * @return ResultInterface
     * @throws LocalizedException
     */
    private function processBlogReturn(Post $model, array $data, ResultInterface $resultRedirect): ResultInterface
    {
        $redirect = $data['back'] ?? 'close';

        if ($redirect ==='continue') {
            $resultRedirect->setPath('*/*/edit', ['post_id' => $model->getId()]);
        } elseif ($redirect === 'close') {
            $resultRedirect->setPath('*/*/');
        } elseif ($redirect === 'duplicate') {
            $duplicateModel = $this->postFactory->create(['data' => $data]);
            $duplicateModel->setId(null);
            $duplicateModel->setIsActive(IsActive::STATUS_DISABLED);
            $this->postRepository->save($duplicateModel);
            $id = $duplicateModel->getId();
            $this->messageManager->addSuccessMessage(__('You duplicated the post.'));
            $this->dataPersistor->set('blog_post', $data);
            $resultRedirect->setPath('*/*/edit', ['post_id' => $id]);
        }

        return $resultRedirect;
    }
}
