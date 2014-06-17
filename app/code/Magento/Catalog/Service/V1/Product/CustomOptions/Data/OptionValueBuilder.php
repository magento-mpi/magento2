<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions\Data;

class OptionValueBuilder extends \Magento\Framework\Service\Data\AbstractObjectBuilder
{
    /**
     * Set option value code
     *
     * @param string $value
     * @return $this
     */
    public function setId($value)
    {
        return $this->_set(OptionValue::CODE, $value);
    }

    /**
     * Set value
     *
     * @param string $value
     * @return $this
     */
    public function setValue($value)
    {
        return $this->_set(OptionValue::VALUE, $value);
    }
}
