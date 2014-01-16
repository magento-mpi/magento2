<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Shipment tracking control form
 *
 */
namespace Magento\Shipping\Block\Adminhtml\Order\Tracking;

class View extends \Magento\Shipping\Block\Adminhtml\Order\Tracking
{
    /**
     * Prepares layout of block
     *
     * @return \Magento\Sales\Block\Adminhtml\Order\View\Giftmessage
     */
    protected function _prepareLayout()
    {
        $onclick = "submitAndReloadArea($('shipment_tracking_info').parentNode, '".$this->getSubmitUrl()."')";
        $this->addChild('save_button', 'Magento\Backend\Block\Widget\Button', array(
            'label'   => __('Add'),
            'class'   => 'save',
            'onclick' => $onclick
        ));
    }

    /**
     * Retrieve save url
     *
     * @return string
     */
    public function getSubmitUrl()
    {
        return $this->getUrl('sales/*/addTrack/', array('shipment_id'=>$this->getShipment()->getId()));
    }

    /**
     * Retrieve save button html
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
     * @param $track
     * @return string
     */
    public function getRemoveUrl($track)
    {
        return $this->getUrl('sales/*/removeTrack/', array(
            'shipment_id' => $this->getShipment()->getId(),
            'track_id' => $track->getId()
        ));
    }

    public function getCarrierTitle($code)
    {
        $carrier = $this->_shippingConfig->getCarrierInstance($code);
        if ($carrier) {
            return $carrier->getConfigData('title');
        } else {
            return __('Custom Value');
        }
        return false;
    }
}
