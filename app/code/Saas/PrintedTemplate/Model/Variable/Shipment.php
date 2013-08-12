<?php
/**
 * {license_notice}
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Container for Magento_Sales_Model_Order_Shipment for shipment variable
 *
 * Container that can restrict access to properties and method
 * with black list or white list.
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Variable_Shipment extends Saas_PrintedTemplate_Model_Variable_Abstract_Entity
{
    /**
     * Key for config
     *
     * @see Saas_PrintedTemplate_Model_Variable_Abstract::_setListsFromConfig()
     * @var string
     */
    protected $_type = 'shipment';

    /**
     * Constructor
     *
     * @see Saas_PrintedTemplate_Model_Template_Variable_Abstract::__construct()
     * @param Magento_Sales_Model_Order_Shipment $value
     */
    public function __construct(Magento_Sales_Model_Order_Shipment $value)
    {
        parent::__construct($value);
        $this->_setListsFromConfig($this->_type);
    }

    /**
     * Retuns shipiment tracking info as HTML table
     *
     * @return string HTML table
     */
    public function getTracks()
    {
        return Mage::getBlockSingleton('Saas_PrintedTemplate_Block_ShipmentTrack')
            ->setTracks($this->_value->getTracksCollection())
            ->toHtml();
    }
}
