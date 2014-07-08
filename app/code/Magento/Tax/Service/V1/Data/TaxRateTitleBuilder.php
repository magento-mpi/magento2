<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1\Data;

/**
 * Builder for the TaxRate Service Data Object
 *
 * @method TaxRate create()
 */
class TaxRateTitleBuilder extends \Magento\Framework\Service\Data\AbstractObjectBuilder
{
    /**
     * Set store id
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->_set(TaxRateTitle::KEY_STORE_ID, $storeId);
        return $this;
    }

    /**
     * Set title value
     *
     * @param string $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->_set(TaxRateTitle::KEY_VALUE_ID, $value);
        return $this;
    }
}
