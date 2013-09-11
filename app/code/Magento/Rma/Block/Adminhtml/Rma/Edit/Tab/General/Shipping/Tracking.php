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

namespace Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General\Shipping;

class Tracking extends \Magento\Adminhtml\Block\Template
{
    /**
     * Retrieve shipment model instance
     *
     * @return \Magento\Sales\Model\Order\Shipment
     */
    public function getRma()
    {
        return \Mage::registry('current_rma');
    }

    /**
     * Gets available carriers
     *
     * @return array
     */
    public function getCarriers()
    {
        return \Mage::helper('Magento\Rma\Helper\Data')->getAllowedShippingCarriers($this->getRma()->getStoreId());
    }

    /**
     * Gets all tracks
     *
     * @return \Magento\Sales\Model\Order\Shipment
     */
    public function getAllTracks()
    {
        return \Mage::getResourceModel('\Magento\Rma\Model\Resource\Shipping\Collection')
            ->addFieldToFilter('rma_entity_id', $this->getRma()->getId())
            ->addFieldToFilter('is_admin', array("neq" => \Magento\Rma\Model\Shipping::IS_ADMIN_STATUS_ADMIN_LABEL))
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
            $this->getLayout()->createBlock('\Magento\Adminhtml\Block\Widget\Button')
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
     * @return \Magento\Sales\Model\Order\Shipment
     */
    public function getShipment()
    {
        return \Mage::registry('current_shipment');
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
     * @param \Magento\Rma\Model\Shipping $track
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
        $carrier = \Mage::getSingleton('Magento\Shipping\Model\Config')->getCarrierInstance($code);
        return $carrier ? $carrier->getConfigData('title') : __('Custom Value');
    }
}
