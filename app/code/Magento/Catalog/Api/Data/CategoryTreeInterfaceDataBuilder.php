<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api\Data;

/**
 * DataBuilder class for \Magento\Catalog\Api\Data\CategoryInterface
 */
class CategoryTreeInterfaceDataBuilder extends \Magento\Framework\Api\ExtensibleDataBuilder
{
    /**
     * @param int|null $categoryId
     */
    public function setId($id)
    {
        $this->data['entity_id'] = $id;
        return $this;
    }

    /**
     * @param int|null $position
     */
    public function setPosition($position)
    {
        $this->data['position'] = $position;
        return $this;
    }

    /**
     * @param int|null $level
     */
    public function setLevel($level)
    {
        $this->data['level'] = $level;
        return $this;
    }

    /**
     * @param int|null $parentId
     */
    public function setParentId($parentId)
    {
        $this->data['parent_id'] = $parentId;
        return $this;
    }

    /**
     * @param string|null $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->data['created_at'] = $createdAt;
        return $this;
    }

    /**
     * @param string|null $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->data['updated_at'] = $updatedAt;
        return $this;
    }

    /**
     * @param string|null $name
     */
    public function setName($name)
    {
        $this->data['name'] = $name;
        return $this;
    }

    /**
     * @param string|null $urlKey
     */
    public function setUrlKey($urlKey)
    {
        $this->data['url_key'] = $urlKey;
        return $this;
    }

    /**
     * @param string|null $path
     */
    public function setPath($path)
    {
        $this->data['path'] = $path;
        return $this;
    }

    /**
     * @param string|null $displayMode
     */
    public function setDisplayMode($displayMode)
    {
        $this->data['display_mode'] = $displayMode;
        return $this;
    }

    /**
     * @param string $availableSortBy
     */
    public function setAvailableSortBy($availableSortBy)
    {
        $this->data['available_sort_by'] = $availableSortBy;
        return $this;
    }

    /**
     * @param bool|null $includeInMenu
     */
    public function setIncludeInMenu($includeInMenu)
    {
        $this->data['include_in_menu'] = $includeInMenu;
        return $this;
    }

    /**
     * @param bool|null $isActive
     */
    public function setIsActive($isActive)
    {
        $this->data['is_active'] = $isActive;
        return $this;
    }

    /**
     * Set product count
     *
     * @param int $productCount
     * @return int
     */
    public function setProductCount($productCount)
    {
        $this->data['product_count'] = $productCount;
        return $this;
    }

    /**
     * Set category level
     *
     * @param \Magento\Catalog\Api\Data\CategoryTreeInterface[] $children
     * @return $this
     */
    public function setChildrenData(array $children)
    {
        $this->data['children_data'] = $children;
        return $this;
    }

    /**
     * Initialize the builder
     *
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        parent::__construct($objectManager, 'Magento\Catalog\Api\Data\CategoryTreeInterface');
    }
}
