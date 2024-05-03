<?php
/**
 * Copyright © 2023, Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Space\Blog\Controller\Adminhtml\Blog;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Ui\Component\MassAction\Filter;
use Space\Blog\Model\ResourceModel\Blog\CollectionFactory;
use Space\Blog\Model\BlogRepository;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Space\Blog\Model\Blog;
use Magento\Framework\Controller\ResultFactory;

class MassEnable extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    public const ADMIN_RESOURCE = 'Space_Blog::blog';

    /**
     * @var Filter
     */
    private Filter $filter;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * @var BlogRepository
     */
    private BlogRepository $blogRepository;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param BlogRepository $blogRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        BlogRepository $blogRepository
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->blogRepository = $blogRepository;
        parent::__construct($context);
    }

    /**
     * Mass enable action
     *
     * @return Redirect|ResultInterface|ResponseInterface
     * @throws LocalizedException
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function execute(): Redirect|ResultInterface|ResponseInterface
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());

        /** @var Blog $blog */
        foreach ($collection as $blog) {
            $blog->setIsActive(true);
            $this->blogRepository->save($blog);
        }

        $this->messageManager->addSuccessMessage(
            __('A total of %1 record(s) have been enabled.', $collection->getSize())
        );

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
