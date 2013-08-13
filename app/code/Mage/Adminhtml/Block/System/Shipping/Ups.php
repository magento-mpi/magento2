<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml shipping UPS content block
 */
class Mage_Adminhtml_Block_System_Shipping_Ups extends Mage_Backend_Block_Template
{
    /**
     * @var Mage_Usa_Model_Shipping_Carrier_Ups
     */
    protected $_shippingModel;

    /**
     * @var Mage_Core_Model_Website
     */
    protected $_websiteModel;

    /**
     * @param Mage_Backend_Block_Template_Context $context
     * @param Mage_Usa_Model_Shipping_Carrier_Ups $shippingModel
     * @param Mage_Core_Model_Website $websiteModel
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Block_Template_Context $context,
        Mage_Usa_Model_Shipping_Carrier_Ups $shippingModel,
        Mage_Core_Model_Website $websiteModel,
        array $data = array()
    ) {
        $this->_shippingModel = $shippingModel;
        $this->_websiteModel = $websiteModel;
        parent::__construct($context, $data);
    }

    /**
     * Get shipping model
     *
     * @return Mage_Usa_Model_Shipping_Carrier_Ups
     */
    public function getShippingModel()
    {
        return $this->_shippingModel;
    }

    /**
     * Get website model
     *
     * @return Mage_Core_Model_Website
     */
    public function getWebsiteModel()
    {
        return $this->_websiteModel;
    }
}
