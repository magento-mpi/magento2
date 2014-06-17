<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions\Data;

class Option extends \Magento\Framework\Service\Data\AbstractObject
{
    const OPTION_ID = 'option_id';
    const TITLE = 'title';
    const TYPE = 'type';
    const SORT_ORDER = 'sort_order';
    const IS_REQUIRE = 'is_require';
    const VALUE = 'value';

    /**
     * Get option id
     *
     * @return string
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
     */
    public function getIsRequire()
    {
        return $this->_get(self::IS_REQUIRE);
    }

    /**
     * Get option value
     *
     * @return \Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionValue[]
     */
    public function getValue()
    {
        return $this->_get(self::VALUE);
    }
}
