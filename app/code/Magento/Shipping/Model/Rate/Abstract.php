<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */


abstract class Magento_Shipping_Model_Rate_Abstract extends Magento_Core_Model_Abstract
{
    static protected $_instances;

    public function getCarrierInstance()
    {
        $code = $this->getCarrier();
        if (!isset(self::$_instances[$code])) {
            self::$_instances[$code] = Mage::getModel('Magento_Shipping_Model_Config')->getCarrierInstance($code);
        }
        return self::$_instances[$code];
    }
}
