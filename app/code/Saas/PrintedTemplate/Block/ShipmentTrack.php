<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Widget to display trackiing numders of shipment
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Blocks
 *
 * @method Magento_Sales_Model_Resource_Order_Shipment_Track_Collection getTracks() Returns collection or empty array
 */
class Saas_PrintedTemplate_Block_ShipmentTrack extends Magento_Backend_Block_Template
{
    /**
     * Initializes block
     *
     * @see Magento_Core_Block_Template::_construct()
     */
    protected function _construct()
    {
        $this->setTemplate('Saas_PrintedTemplate::shipment_track.phtml')
            ->setData('tracks', array());
    }

    /**
     * Set tracks
     *
     * @param Magento_Sales_Model_Resource_Order_Shipment_Track_Collection $collection
     */
    public function setTracks( Magento_Sales_Model_Resource_Order_Shipment_Track_Collection $collection)
    {
        return $this->setData('tracks', $collection);
    }

    /**
     * If tracks collection is empty return empty string.
     *
     * @return string HTML
     * @see Magento_Core_Block_Template::_toHtml()
     */
    protected function _toHtml()
    {
        return count($this->getTracks()) ? parent::_toHtml() : '';
    }
}
