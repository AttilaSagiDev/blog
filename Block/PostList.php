<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\Blog\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Space\Blog\Model\ResourceModel\Post\CollectionFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Space\Blog\Api\PostRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Space\Blog\Api\Data\PostInterface;
use Space\Blog\Model\ResourceModel\Post\Collection;
use Magento\Theme\Block\Html\Pager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Space\Blog\Api\Data\PostSearchResultsInterface;

class PostList extends Template
{
    /**
     * Posts limit
     */
    private const LIMIT = 10;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var PostRepositoryInterface
     */
    private PostRepositoryInterface $postRepository;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * Constructor
     *
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param PostRepositoryInterface $postRepository
     * @param StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CollectionFactory $collectionFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        PostRepositoryInterface $postRepository,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->postRepository = $postRepository;
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    /**
     * Get posts
     *
     * @return Collection
     * @throws NoSuchEntityException
     */
    public function getPosts(): Collection
    {
        $page = $this->getRequest()->getParam('p') ?: 1;
        $limit = $this->getRequest()->getParam('limit') ?: self::LIMIT;

        $storeId = $this->storeManager->getStore()->getId();
        $collection = $this->collectionFactory->create();
        $collection->addStoreFilter((int)$storeId)
            ->addFieldToFilter(PostInterface::IS_ACTIVE, ['eq' => 1])
            ->setPageSize($limit)
            ->setCurPage($page)
            ->setOrder(PostInterface::BLOG_ID, 'DESC');

        return $collection;
    }

    /**
     * Get post results for testing
     * @deprecated because not used
     *
     * @param int $page
     * @param int $limit
     * @return PostSearchResultsInterface
     * @throws LocalizedException
     */
    public function getPostResults(int $page, int $limit): PostSearchResultsInterface
    {
        $this->searchCriteriaBuilder->addFilter(PostInterface::IS_ACTIVE, 1);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $searchCriteria->setCurrentPage($page)
            ->setPageSize($limit);

        return $this->postRepository->getList($searchCriteria);
    }

    /**
     * Truncate string
     *
     * @param string $value
     * @param int $length
     * @param string $etc
     * @param bool $breakWords
     * @return string
     */
    public function truncateString(
        string $value,
        int $length = 80,
        string $etc = '...',
        bool $breakWords = true
    ): string {
        return $this->filterManager->truncate(
            $value,
            ['length' => $length, 'etc' => $etc, 'breakWords' => $breakWords]
        );
    }

    /**
     * Get view url
     *
     * @param int $postId
     * @return string
     */
    public function getViewUrl(int $postId): string
    {
        return $this->getUrl(
            'blog/post/view',
            ['id' => $postId]
        );
    }

    /**
     * Prepare layout
     *
     * @return $this|PostList
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    protected function _prepareLayout(): PostList|static
    {
        parent::_prepareLayout();
        if ($this->getPosts()) {
            $pager = $this->getLayout()->createBlock(
                Pager::class,
                'space.blog.list.pager'
            )->setCollection(
                $this->getPosts()
            );
            $this->setChild('pager', $pager);
            $this->getPosts()->load();
        }
        return $this;
    }

    /**
     * Get Pager child block output
     *
     * @return string
     */
    public function getPagerHtml(): string
    {
        return $this->getChildHtml('pager');
    }
}
