<?php/**
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
class CategoryInterfaceDataBuilder extends \Magento\Framework\Api\ExtensibleDataBuilder
{
    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->data['entity_id'] = $id;
        return $this;
    }

    /**
     * @param int $parentId
     */
    public function setParentId($parentId)
    {
        $this->data['parent_id'] = $parentId;
        return $this;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->data['name'] = $name;
        return $this;
    }

    /**
     * @param bool $isActive
     */
    public function setIsActive($isActive)
    {
        $this->data['is_active'] = $isActive;
        return $this;
    }

    /**
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->data['position'] = $position;
        return $this;
    }

    /**
     * @param int $level
     */
    public function setLevel($level)
    {
        $this->data['level'] = $level;
        return $this;
    }

    /**
     * @param array|null $children
     */
    public function setChildren($children)
    {
        $this->data['children'] = $children;
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
     * Initialize the builder
     *
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        parent::__construct($objectManager, 'Magento\Catalog\Api\Data\CategoryInterface');
    }
}
