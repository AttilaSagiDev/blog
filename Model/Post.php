<?php
/**
 * Copyright (c) 2024 Attila Sagi
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */

declare(strict_types=1);

namespace Space\Blog\Model;

use Magento\Framework\Model\AbstractModel;
use Space\Blog\Api\Data\PostInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Space\Blog\Model\ResourceModel\Post as PostResourceModel;

/**
 * @method Post setStoreId(int $storeId)
 * @method int getStoreId()
 */
class Post extends AbstractModel implements PostInterface, IdentityInterface
{
    /**
     * Post cache tag
     */
    public const CACHE_TAG = 'space_blog';

    /**
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'space_blog';

    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Resource and model initialization
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(PostResourceModel::class);
    }

    /**
     * Get identities
     *
     * @return string[]
     */
    public function getIdentities(): array
    {
        return [self::CACHE_TAG . '_' . $this->getId(), self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return (int)$this->getData(self::BLOG_ID);
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->getData(self::TITLE);
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent(): string
    {
        return $this->getData(self::CONTENT);
    }

    /**
     * Get author
     *
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->getData(self::AUTHOR);
    }

    /**
     * Get creation time
     *
     * @return string|null
     */
    public function getCreationTime(): ?string
    {
        return $this->getData(self::CREATION_TIME);
    }

    /**
     * Get update time
     *
     * @return string|null
     */
    public function getUpdateTime(): ?string
    {
        return $this->getData(self::UPDATE_TIME);
    }

    /**
     * Is active
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return (bool)$this->getData(self::IS_ACTIVE);
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return PostInterface
     */
    public function setId($id): PostInterface
    {
        return $this->setData(self::BLOG_ID, $id);
    }

    /**
     * Set title
     *
     * @param string $title
     * @return PostInterface
     */
    public function setTitle(string $title): PostInterface
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Set content
     *
     * @param string $content
     * @return PostInterface
     */
    public function setContent(string $content): PostInterface
    {
        return $this->setData(self::CONTENT, $content);
    }

    /**
     * Set author
     *
     * @param string $author
     * @return PostInterface
     */
    public function setAuthor(string $author): PostInterface
    {
        return $this->setData(self::AUTHOR, $author);
    }

    /**
     * Set creation time
     *
     * @param string $creationTime
     * @return PostInterface
     */
    public function setCreationTime(string $creationTime): PostInterface
    {
        return $this->setData(self::CREATION_TIME, $creationTime);
    }

    /**
     * Set update time
     *
     * @param string $updateTime
     * @return PostInterface
     */
    public function setUpdateTime(string $updateTime): PostInterface
    {
        return $this->setData(self::UPDATE_TIME, $updateTime);
    }

    /**
     * Set is active
     *
     * @param bool|int $isActive
     * @return PostInterface
     */
    public function setIsActive(bool|int $isActive): PostInterface
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * Receive post store ids
     *
     * @return int[]
     */
    public function getStores(): array
    {
        return $this->hasData('stores') ? $this->getData('stores') : $this->getData('store_id');
    }
}
