<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Service\V1\Data;

use Magento\Framework\Api\ExtensibleObjectBuilder;

/**
 * Builder for the TaxClassKey Service Data Object
 *
 * @method TaxClassKey create()
 */
class TaxClassKeyBuilder extends ExtensibleObjectBuilder
{
    /**
     * Set type of tax class key
     *
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        return $this->_set(TaxClassKey::KEY_TYPE, $type);
    }

    /**
     * Set value of tax class key
     *
     * @param String $value
     * @return $this
     */
    public function setValue($value)
    {
        return $this->_set(TaxClassKey::KEY_VALUE, $value);
    }
}
