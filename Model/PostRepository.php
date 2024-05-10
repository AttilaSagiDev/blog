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
use Space\Blog\Model\ResourceModel\Post as ResourcePost;
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
     * @var ResourcePost
     */
    protected ResourcePost $resource;

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
     * @param ResourcePost $resource
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
        ResourcePost $resource,
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
     * @param PostInterface $post
     * @return PostInterface
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException|LocalizedException
     */
    public function save(PostInterface $post): PostInterface
    {
        if (empty($post->getStoreId())) {
            $post->setStoreId($this->storeManager->getStore()->getId());
        }

        if ($post->getId() && $post instanceof Post && !$post->getOrigData()) {
            $post = $this->hydrator->hydrate($this->getById($post->getId()), $this->hydrator->extract($post));
        }

        try {
            $this->resource->save($post);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $post;
    }

    /**
     * Retrieve post
     *
     * @param int $postId
     * @return PostInterface
     * @throws NoSuchEntityException|LocalizedException
     */
    public function getById(int $postId): PostInterface
    {
        $post = $this->postFactory->create();
        $this->resource->load($post, $postId);
        if (!$post->getId()) {
            throw new NoSuchEntityException(__('The post with the "%1" ID doesn\'t exist.', $postId));
        }

        return $post;
    }

    /**
     * Retrieve posts matching the specified criteria
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
     * Delete post
     *
     * @param PostInterface $post
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(PostInterface $post): bool
    {
        try {
            $this->resource->delete($post);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }

        return true;
    }

    /**
     * Delete by ID
     *
     * @param int $postId
     * @return bool
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById(int $postId): bool
    {
        return $this->delete($this->getById($postId));
    }
}
