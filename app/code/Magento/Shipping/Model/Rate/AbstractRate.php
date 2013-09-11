<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Shipping\Model\Rate;

abstract class AbstractRate extends \Magento\Core\Model\AbstractModel
{
    static protected $_instances;

    public function getCarrierInstance()
    {
        $code = $this->getCarrier();
        if (!isset(self::$_instances[$code])) {
            self::$_instances[$code] = \Mage::getModel('\Magento\Shipping\Model\Config')->getCarrierInstance($code);
        }
        return self::$_instances[$code];
    }
}
