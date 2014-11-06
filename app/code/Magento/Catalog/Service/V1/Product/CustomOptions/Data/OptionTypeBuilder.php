<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions\Data;

/**
 * @codeCoverageIgnore
 */
class OptionTypeBuilder extends \Magento\Framework\Api\ExtensibleObjectBuilder
{
    /**
     * Set option type label
     *
     * @param string $value
     * @return $this
     */
    public function setLabel($value)
    {
        return $this->_set(OptionType::LABEL, $value);
    }

    /**
     * Set option type code
     *
     * @param string $value
     * @return $this
     */
    public function setCode($value)
    {
        return $this->_set(OptionType::CODE, $value);
    }

    /**
     * Set option type group
     *
     * @param string $value
     * @return $this
     */
    public function setGroup($value)
    {
        return $this->_set(OptionType::GROUP, $value);
    }
}
