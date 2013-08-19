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
class Magento_Adminhtml_Block_Sales_Order_Invoice_Create_Tracking extends Magento_Adminhtml_Block_Template
{
    public function _construct()
    {
        $this->setTemplate('sales/order/invoice/create/tracking.phtml');
    }

    /**
     * Prepares layout of block
     *
     * @return Magento_Adminhtml_Block_Sales_Order_View_Giftmessage
     */
    protected function _prepareLayout()
    {
        $this->addChild('add_button', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label'   => __('Add Tracking Number'),
            'class'   => '',
            'onclick' => 'trackingControl.add()'
        ));
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
     * Retrieve shipment model instance
     *
     * @return Magento_Sales_Model_Order_Shipment
     */
    public function getInvoice()
    {
        return Mage::registry('current_invoice');
    }

    /**
     * Retrieve
     *
     * @return unknown
     */
    public function getCarriers()
    {

        $carriers = array();
        $carrierInstances = Mage::getSingleton('Magento_Shipping_Model_Config')->getAllCarriers(
            $this->getInvoice()->getStoreId()
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
