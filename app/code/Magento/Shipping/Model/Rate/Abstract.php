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
    /**
     * @var array
     */
    static protected $_instances;

    /**
     * @var Magento_Shipping_Model_Config
     */
    protected $_shippingConfig;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Shipping_Model_Config $shippingConfig
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Shipping_Model_Config $shippingConfig,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_shippingConfig = $shippingConfig;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    public function getCarrierInstance()
    {
        $code = $this->getCarrier();
        if (!isset(self::$_instances[$code])) {
            self::$_instances[$code] = $this->_shippingConfig->getCarrierInstance($code);
        }
        return self::$_instances[$code];
    }
}
