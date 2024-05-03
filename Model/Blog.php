<?php
/**
 * Copyright © 2023, Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Space\Blog\Model;

use Magento\Framework\Model\AbstractModel;
use Space\Blog\Api\Data\BlogInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Space\Blog\Model\ResourceModel\Blog as BlogResourceModel;

/**
 * @method Blog setStoreId(int $storeId)
 * @method int getStoreId()
 */
class Blog extends AbstractModel implements BlogInterface, IdentityInterface
{
    /**
     * Blog cache tag
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
        $this->_init(BlogResourceModel::class);
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
     * @return BlogInterface
     */
    public function setId($id): BlogInterface
    {
        return $this->setData(self::BLOG_ID, $id);
    }

    /**
     * Set title
     *
     * @param string $title
     * @return BlogInterface
     */
    public function setTitle(string $title): BlogInterface
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Set content
     *
     * @param string $content
     * @return BlogInterface
     */
    public function setContent(string $content): BlogInterface
    {
        return $this->setData(self::CONTENT, $content);
    }

    /**
     * Set author
     *
     * @param string $author
     * @return BlogInterface
     */
    public function setAuthor(string $author): BlogInterface
    {
        return $this->setData(self::AUTHOR, $author);
    }

    /**
     * Set creation time
     *
     * @param string $creationTime
     * @return BlogInterface
     */
    public function setCreationTime(string $creationTime): BlogInterface
    {
        return $this->setData(self::CREATION_TIME, $creationTime);
    }

    /**
     * Set update time
     *
     * @param string $updateTime
     * @return BlogInterface
     */
    public function setUpdateTime(string $updateTime): BlogInterface
    {
        return $this->setData(self::UPDATE_TIME, $updateTime);
    }

    /**
     * Set is active
     *
     * @param bool|int $isActive
     * @return BlogInterface
     */
    public function setIsActive(bool|int $isActive): BlogInterface
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * Receive blog store ids
     *
     * @return int[]
     */
    public function getStores(): array
    {
        return $this->hasData('stores') ? $this->getData('stores') : $this->getData('store_id');
    }
}
