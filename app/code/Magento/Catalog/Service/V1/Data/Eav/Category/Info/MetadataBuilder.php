<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Eav\Category\Info;

use Magento\Framework\Service\Data\Eav\AbstractObjectBuilder;

class MetadataBuilder extends AbstractObjectBuilder
{
    /**
     * @param int $value
     * @return $this
     */
    public function setCategoryId($value)
    {
        $this->_set(Metadata::ID, $value);
        return $this;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setActive($value)
    {
        $this->_set(Metadata::ACTIVE, $value);
        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setPosition($value)
    {
        $this->_set(Metadata::POSITION, $value);
        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setLevel($value)
    {
        $this->_set(Metadata::LEVEL, $value);
        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setParentId($value)
    {
        $this->_set(Metadata::PARENT_ID, $value);
        return $this;
    }

    /**
     * @param int[] $value
     * @return $this
     */
    public function setChildren($value)
    {
        $this->_set(Metadata::CHILDREN, $value);
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setCreatedAt($value)
    {
        $this->_set(Metadata::CREATED_AT, $value);
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setUpdatedAt($value)
    {
        $this->_set(Metadata::UPDATED_AT, $value);
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setName($value)
    {
        $this->_set(Metadata::NAME, $value);
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setUrlKey($value)
    {
        $this->_set(Metadata::URL_KEY, $value);
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setPath($value)
    {
        $this->_set(Metadata::PATH, $value);
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setDisplayMode($value)
    {
        $this->_set(Metadata::DISPLAY_MODE, $value);
        return $this;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setAnchor($value)
    {
        $this->_set(Metadata::ANCHOR, $value);
        return $this;
    }

    /**
     * @param string[] $value
     * @return $this
     */
    public function setAvailableSortBy($value)
    {
        $this->_set(Metadata::AVAILABLE_SORT_BY, $value);
        return $this;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setIncludeInMenu($value)
    {
        $this->_set(Metadata::INCLUDE_IN_MENU, $value);
        return $this;
    }
}
