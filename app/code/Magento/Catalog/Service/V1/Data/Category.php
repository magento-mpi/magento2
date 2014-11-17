<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data;

use \Magento\Framework\Api\AbstractExtensibleObject;

/**
 * @codeCoverageIgnore
 */
class Category extends AbstractExtensibleObject
{
    const ID = 'id';

    const PARENT_ID = 'parent_id';

    const PATH = 'path';

    const POSITION = 'position';

    const LEVEL = 'level';

    const CHILDREN_COUNT = 'children_count';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    const NAME = 'name';

    const ACTIVE = 'active';

    /**
     * Category id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * Category parent id
     *
     * @return int|null
     */
    public function getParentId()
    {
        return $this->_get(self::PARENT_ID);
    }

    /**
     * Path of the category
     *
     * @return string|null
     */
    public function getPath()
    {
        return $this->_get(self::PATH);
    }

    /**
     * Position of the category
     *
     * @return int|null
     */
    public function getPosition()
    {
        return $this->_get(self::POSITION);
    }

    /**
     * Category level
     *
     * @return int|null
     */
    public function getLevel()
    {
        return $this->_get(self::LEVEL);
    }

    /**
     * Category children count
     *
     * @return int|null
     */
    public function getChildrenCount()
    {
        return $this->_get(self::CHILDREN_COUNT);
    }

    /**
     * Category created date
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->_get(self::CREATED_AT);
    }

    /**
     * Category updated date
     *
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return $this->_get(self::UPDATED_AT);
    }

    /**
     * Name of the created category
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->_get(self::NAME);
    }

    /**
     * Defines whether the category will be visible in the frontend
     *
     * @return bool|null
     */
    public function isActive()
    {
        return (bool)$this->_get(self::ACTIVE);
    }
}
