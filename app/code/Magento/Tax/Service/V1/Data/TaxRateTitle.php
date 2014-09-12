<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1\Data;

/**
 * A localized tax rate title associated with a store view.
 */
class TaxRateTitle extends \Magento\Framework\Service\Data\AbstractExtensibleObject
{
    /**#@+
     *
     * Tax rate field key.
     */
    const KEY_STORE_ID = 'store_id';

    const KEY_VALUE_ID = 'value';
    /**#@-*/

    /**
     * Get store id
     *
     * @return string
     */
    public function getStoreId()
    {
        return $this->_get(self::KEY_STORE_ID);
    }

    /**
     * Get title value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->_get(self::KEY_VALUE_ID);
    }
}
