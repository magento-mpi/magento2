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
 * Shipment tracking control form
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Sales\Order\Shipment\Create;

class Tracking extends \Magento\Adminhtml\Block\Template
{
    /**
     * Prepares layout of block
     *
     * @return \Magento\Adminhtml\Block\Sales\Order\View\Giftmessage
     */
    protected function _prepareLayout()
    {
        $this->addChild('add_button', '\Magento\Adminhtml\Block\Widget\Button', array(
            'label'   => __('Add Tracking Number'),
            'class'   => '',
            'onclick' => 'trackingControl.add()'
        ));

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
     * Retrieve
     *
     * @return unknown
     */
    public function getCarriers()
    {
        $carriers = array();
        $carrierInstances = \Mage::getSingleton('Magento\Shipping\Model\Config')->getAllCarriers(
            $this->getShipment()->getStoreId()
        );
        $carriers['custom'] = __('Custom Value');
        foreach ($carrierInstances as $code => $carrier) {
            if ($carrier->isTrackingAvailable()) {
                $carriers[$code] = $carrier->getConfigData('title');
            }
        }
        return $carriers;
    }
}
