<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */


abstract class Mage_Shipping_Model_Rate_Abstract extends Magento_Core_Model_Abstract
{
    static protected $_instances;

    public function getCarrierInstance()
    {
        $code = $this->getCarrier();
        if (!isset(self::$_instances[$code])) {
            self::$_instances[$code] = Mage::getModel('Mage_Shipping_Model_Config')->getCarrierInstance($code);
        }
        return self::$_instances[$code];
    }
}
