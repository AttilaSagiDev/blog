<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\Blog\Model;

use Space\Blog\Api\PostRepositoryInterface;
use Space\Blog\Api\Data;
use Space\Blog\Api\Data\PostInterface;
use Space\Blog\Model\ResourceModel\Post as ResourceBlog;
use Space\Blog\Model\ResourceModel\Post\CollectionFactory as PostCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;
use Space\Blog\Api\Data\PostInterfaceFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;

class PostRepository implements PostRepositoryInterface
{
    /**
     * @var ResourceBlog
     */
    protected ResourceBlog $resource;

    /**
     * @var PostFactory
     */
    protected PostFactory $postFactory;

    /**
     * @var PostCollectionFactory
     */
    protected PostCollectionFactory $postCollectionFactory;

    /**
     * @var Data\PostSearchResultsInterfaceFactory
     */
    protected Data\PostSearchResultsInterfaceFactory $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    protected DataObjectHelper $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected DataObjectProcessor $dataObjectProcessor;

    /**
     * @var PostInterfaceFactory
     */
    protected PostInterfaceFactory $dataPostFactory;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var CollectionProcessorInterface
     */
    private CollectionProcessorInterface $collectionProcessor;

    /**
     * @var HydratorInterface
     */
    private HydratorInterface $hydrator;

    /**
     * @param ResourceBlog $resource
     * @param PostFactory $postFactory
     * @param PostInterfaceFactory $dataPostFactory
     * @param PostCollectionFactory $postCollectionFactory
     * @param Data\PostSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface|null $collectionProcessor
     * @param HydratorInterface|null $hydrator
     */
    public function __construct(
        ResourceBlog $resource,
        PostFactory $postFactory,
        PostInterfaceFactory $dataPostFactory,
        PostCollectionFactory $postCollectionFactory,
        Data\PostSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor = null,
        ?HydratorInterface $hydrator = null
    ) {
        $this->resource = $resource;
        $this->postFactory = $postFactory;
        $this->postCollectionFactory = $postCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPostFactory = $dataPostFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor ?:
            ObjectManager::getInstance()->get(CollectionProcessorInterface::class);
        $this->hydrator = $hydrator ?? ObjectManager::getInstance()->get(HydratorInterface::class);
    }

    /**
     * Save
     *
     * @param PostInterface $blog
     * @return PostInterface
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException|LocalizedException
     */
    public function save(PostInterface $blog): PostInterface
    {
        if (empty($blog->getStoreId())) {
            $blog->setStoreId($this->storeManager->getStore()->getId());
        }

        if ($blog->getId() && $blog instanceof Post && !$blog->getOrigData()) {
            $blog = $this->hydrator->hydrate($this->getById($blog->getId()), $this->hydrator->extract($blog));
        }

        try {
            $this->resource->save($blog);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $blog;
    }

    /**
     * Retrieve blog
     *
     * @param int $blogId
     * @return PostInterface
     * @throws NoSuchEntityException|LocalizedException
     */
    public function getById(int $blogId): PostInterface
    {
        $blog = $this->postFactory->create();
        $this->resource->load($blog, $blogId);
        if (!$blog->getId()) {
            throw new NoSuchEntityException(__('The post with the "%1" ID doesn\'t exist.', $blogId));
        }

        return $blog;
    }

    /**
     * Retrieve blogs matching the specified criteria
     *
     * @param SearchCriteriaInterface $criteria
     * @return Data\PostSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $criteria): Data\PostSearchResultsInterface
    {
        $collection = $this->postCollectionFactory->create();
        $this->collectionProcessor->process($criteria, $collection);

        /** @var Data\PostSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * Delete blog
     *
     * @param PostInterface $blog
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(PostInterface $blog): bool
    {
        try {
            $this->resource->delete($blog);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }

        return true;
    }

    /**
     * Delete by ID
     *
     * @param int $blogId
     * @return bool
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById(int $blogId): bool
    {
        return $this->delete($this->getById($blogId));
    }
}
