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
 * Shipment view form
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Sales_Order_Shipment_View_Form extends Magento_Adminhtml_Block_Sales_Order_Abstract
{
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
     * Retrieve invoice order
     *
     * @return Magento_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->getShipment()->getOrder();
    }

    /**
     * Retrieve source
     *
     * @return Magento_Sales_Model_Order_Shipment
     */
    public function getSource()
    {
        return $this->getShipment();
    }

    /**
     * Get create label button html
     *
     * @return string
     */
    public function getCreateLabelButton()
    {
        $data['shipment_id'] = $this->getShipment()->getId();
        $url = $this->getUrl('*/sales_order_shipment/createLabel', $data);
        return $this->getLayout()
            ->createBlock('Magento_Adminhtml_Block_Widget_Button')
            ->setData(array(
                'label'   => __('Create Shipping Label...'),
                'onclick' => 'packaging.showWindow();',
            ))
            ->toHtml();
    }

    /**
     * Get print label button html
     *
     * @return string
     */
    public function getPrintLabelButton()
    {
        $data['shipment_id'] = $this->getShipment()->getId();
        $url = $this->getUrl('*/sales_order_shipment/printLabel', $data);
        return $this->getLayout()
            ->createBlock('Magento_Adminhtml_Block_Widget_Button')
            ->setData(array(
                'label'   => __('Print Shipping Label'),
                'onclick' => 'setLocation(\'' . $url . '\')'
            ))
            ->toHtml();
    }

    /**
     * Show packages button html
     *
     * @return string
     */
    public function getShowPackagesButton()
    {
        return $this->getLayout()
            ->createBlock('Magento_Adminhtml_Block_Widget_Button')
            ->setData(array(
                'label'   => __('Show Packages'),
                'onclick' => 'showPackedWindow();'
            ))
            ->toHtml();
    }

    /**
     * Check is carrier has functionality of creation shipping labels
     *
     * @return bool
     */
    public function canCreateShippingLabel()
    {
        $shippingCarrier = $this->getOrder()->getShippingCarrier();
        return $shippingCarrier && $shippingCarrier->isShippingLabelsAvailable();
    }
}
