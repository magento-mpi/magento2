<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Service\V1\Data;

class Packages extends \Magento\Framework\Service\Data\AbstractExtensibleObject
{
    /**#@+
     * Constants defined for keys of array
     */
    const PARAMS = 'params';
    const ITEMS = 'items';
    /**#@-*/

    /**
     * Get params
     *
     * @return \Magento\Rma\Service\V1\Data\PackagesParams[]
     */
    public function getParams()
    {
        return $this->_get(self::PARAMS);
    }

    /**
     * Get items
     *
     * @return \Magento\Rma\Service\V1\Data\PackagesItems[]
     */
    public function getItems()
    {
        return $this->_get(self::PARAMS);
    }
}
