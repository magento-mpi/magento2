<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Shipment tracking
 *
 * @category    Magento
 * @package     Magento_RMA
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Rma_Block_Adminhtml_Rma_Edit_Tab_General_Shipping_Tracking extends Magento_Adminhtml_Block_Template
{
    /**
     * Retrieve shipment model instance
     *
     * @return Magento_Sales_Model_Order_Shipment
     */
    public function getRma()
    {
        return Mage::registry('current_rma');
    }

    /**
     * Gets available carriers
     *
     * @return array
     */
    public function getCarriers()
    {
        return Mage::helper('Magento_Rma_Helper_Data')->getAllowedShippingCarriers($this->getRma()->getStoreId());
    }

    /**
     * Gets all tracks
     *
     * @return Magento_Sales_Model_Order_Shipment
     */
    public function getAllTracks()
    {
        return Mage::getResourceModel('Magento_Rma_Model_Resource_Shipping_Collection')
            ->addFieldToFilter('rma_entity_id', $this->getRma()->getId())
            ->addFieldToFilter('is_admin', array("neq" => Magento_Rma_Model_Shipping::IS_ADMIN_STATUS_ADMIN_LABEL))
        ;
    }

    /**
     * Prepares layout of block
     *
     * @return string
     */
    protected function _prepareLayout()
    {
        $onclick = "submitAndReloadArea($('shipment_tracking_info').parentNode, '".$this->getSubmitUrl()."')";
        $this->setChild(
            'save_button',
            $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
                ->setData(
                    array(
                        'label'   => __('Add'),
                        'class'   => 'save',
                        'onclick' => $onclick
                    )
                )
        );
    }

    /**
     * Retrieve shipment model instance
     *
     * @return Magento_Sales_Model_Order_Shipment
     */
    public function getShipment()
    {
        return Mage::registry('current_shipment');
    }

    /**
     * Retrieve save url
     *
     * @return string
     */
    public function getSubmitUrl()
    {
        return $this->getUrl('*/*/addTrack/', array('id' => $this->getRma()->getId()));
    }

    /**
     * Retrive save button html
     *
     * @return string
     */
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    /**
     * Retrieve remove url
     *
     * @param Magento_Rma_Model_Shipping $track
     * @return string
     */
    public function getRemoveUrl($track)
    {
        return $this->getUrl('*/*/removeTrack/', array(
            'id' => $this->getRma()->getId(),
            'track_id' => $track->getId()
        ));
    }

    /**
     * Get Carrier Title
     *
     * @param string $code
     * @return string
     */
    public function getCarrierTitle($code)
    {
        $carrier = Mage::getSingleton('Magento_Shipping_Model_Config')->getCarrierInstance($code);
        return $carrier ? $carrier->getConfigData('title') : __('Custom Value');
    }
}
