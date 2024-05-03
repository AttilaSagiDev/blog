<?php
/**
 * Copyright © 2023, Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Space\Blog\Controller\Adminhtml\Blog;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Space\Blog\Model\BlogFactory;
use Space\Blog\Api\BlogRepositoryInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Backend\Model\View\Result\Redirect;
use Space\Blog\Model\Source\IsActive;
use Space\Blog\Model\Blog;
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
     * @var BlogFactory
     */
    private BlogFactory $blogFactory;

    /**
     * @var BlogRepositoryInterface
     */
    private BlogRepositoryInterface $blogRepository;

    /**
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param BlogFactory|null $blogFactory
     * @param BlogRepositoryInterface|null $blogRepository
     */
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        BlogFactory $blogFactory = null,
        BlogRepositoryInterface $blogRepository = null
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->blogFactory = $blogFactory
            ?: ObjectManager::getInstance()->get(BlogFactory::class);
        $this->blogRepository = $blogRepository
            ?: ObjectManager::getInstance()->get(BlogRepositoryInterface::class);
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
            if (empty($data['blog_id'])) {
                $data['blog_id'] = null;
            }

            /** @var Blog $model */
            $model = $this->blogFactory->create();

            $id = (int)$this->getRequest()->getParam('blog_id');
            if ($id) {
                try {
                    $model = $this->blogRepository->getById($id);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage(__('This post no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }

            $model->setData($data);

            try {
                $this->blogRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the post.'));
                $this->dataPersistor->clear('space_blog');
                return $this->processBlogReturn($model, $data, $resultRedirect);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the post.'));
            }

            $this->dataPersistor->set('space_blog', $data);
            return $resultRedirect->setPath('*/*/edit', ['blog_id' => $id]);
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Process and set the post return
     *
     * @param Blog $model
     * @param array $data
     * @param ResultInterface $resultRedirect
     * @return ResultInterface
     * @throws LocalizedException
     */
    private function processBlogReturn(Blog $model, array $data, ResultInterface $resultRedirect): ResultInterface
    {
        $redirect = $data['back'] ?? 'close';

        if ($redirect ==='continue') {
            $resultRedirect->setPath('*/*/edit', ['blog_id' => $model->getId()]);
        } elseif ($redirect === 'close') {
            $resultRedirect->setPath('*/*/');
        } elseif ($redirect === 'duplicate') {
            $duplicateModel = $this->blogFactory->create(['data' => $data]);
            $duplicateModel->setId(null);
            $duplicateModel->setIsActive(IsActive::STATUS_DISABLED);
            $this->blogRepository->save($duplicateModel);
            $id = $duplicateModel->getId();
            $this->messageManager->addSuccessMessage(__('You duplicated the post.'));
            $this->dataPersistor->set('space_blog', $data);
            $resultRedirect->setPath('*/*/edit', ['blog_id' => $id]);
        }

        return $resultRedirect;
    }
}
