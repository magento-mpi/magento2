<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions\Data;

/**
 * @deprecate
 * @todo remove this interface
 * @see \Magento\Catalog\Api\Data\Product\CustomOptions\OptionInterface
 */
class Option extends \Magento\Framework\Service\Data\AbstractExtensibleObject
{
    const OPTION_ID = 'option_id';
    const TITLE = 'title';
    const TYPE = 'type';
    const SORT_ORDER = 'sort_order';
    const IS_REQUIRE = 'is_require';
    const METADATA = 'metadata';

    /**
     * Get option id
     *
     * @return int|null
     */
    public function getOptionId()
    {
        return $this->_get(self::OPTION_ID);
    }

    /**
     * Get option title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_get(self::TITLE);
    }

    /**
     * Get option type
     *
     * @return string
     */
    public function getType()
    {
        return $this->_get(self::TYPE);
    }

    /**
     * Get sort order
     *
     * @return int
     */
    public function getSortOrder()
    {
        return $this->_get(self::SORT_ORDER);
    }

    /**
     * Get is require
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsRequire()
    {
        return $this->_get(self::IS_REQUIRE);
    }

    /**
     * Get option metadata
     *
     * @return \Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata[]
     */
    public function getMetadata()
    {
        return $this->_get(self::METADATA);
    }
}
