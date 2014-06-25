<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions\Data;

class OptionBuilder extends \Magento\Framework\Service\Data\AbstractObjectBuilder
{
    /**
     * Set option id
     *
     * @param int|null $value
     * @return $this
     */
    public function setOptionId($value)
    {
        return $this->_set(Option::OPTION_ID, $value);
    }

    /**
     * Set option title
     *
     * @param string $value
     * @return $this
     */
    public function setTitle($value)
    {
        return $this->_set(Option::TITLE, $value);
    }

    /**
     * Set option type
     *
     * @param string $value
     * @return $this
     */
    public function setType($value)
    {
        return $this->_set(Option::TYPE, $value);
    }

    /**
     * Set sort order
     *
     * @param int $value
     * @return $this
     */
    public function setSortOrder($value)
    {
        return $this->_set(Option::SORT_ORDER, $value);
    }

    /**
     * Set is require
     *
     * @param bool $value
     * @return $this
     */
    public function setIsRequire($value)
    {
        return $this->_set(Option::IS_REQUIRE, $value);
    }

    /**
     * Set option value
     *
     * @param \Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionValue[] $value
     * @return $this
     */
    public function setValue($value)
    {
        return $this->_set(Option::VALUE, $value);
    }
}
