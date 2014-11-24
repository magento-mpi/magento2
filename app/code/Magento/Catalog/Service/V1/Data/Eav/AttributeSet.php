<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Eav;

/**
 * Contains basic attribute set data
 *
 * @codeCoverageIgnore
 * @deprecated
 * @see \Magento\Catalog\Api\Data\AttributeSetInterface
 */
class AttributeSet extends \Magento\Framework\Api\AbstractExtensibleObject
{
    /**
     * table field for id
     */
    const ID = 'id';

    /**
     * table field for name
     */
    const NAME = 'name';

    /**
     * table field for sort order index
     */
    const ORDER = 'sort_order';

    /**
     * Get attribute set id
     *
     * @return int
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * Get attribute set name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_get(self::NAME);
    }

    /**
     * Get attribute set sort order index
     *
     * @return int
     */
    public function getSortOrder()
    {
        return $this->_get(self::ORDER);
    }
}
