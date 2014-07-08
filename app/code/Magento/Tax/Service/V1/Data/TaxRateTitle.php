<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1\Data;

class TaxRateTitle extends \Magento\Framework\Service\Data\AbstractObject
{
    /**#@+
     * Constants defined for keys of array, makes typos less likely
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
