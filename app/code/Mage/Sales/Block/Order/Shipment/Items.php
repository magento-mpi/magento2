<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales order view items block
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Block_Order_Shipment_Items extends Mage_Sales_Block_Items_Abstract
{
    /**
     * Retrieve current order model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    public function getPrintShipmentUrl($shipment){
        return Mage::getUrl('*/*/printShipment', array('shipment_id' => $shipment->getId()));
    }

    public function getPrintAllShipmentsUrl($order){
        return Mage::getUrl('*/*/printShipment', array('order_id' => $order->getId()));
    }

    /**
     * Get html of shipment comments block
     *
     * @param   Mage_Sales_Model_Order_Shipment $shipment
     * @return  string
     */
    public function getCommentsHtml($shipment)
    {
        $html = '';
        $comments = $this->getChildBlock('shipment_comments');
        if ($comments) {
            $comments->setEntity($shipment)
                ->setTitle(__('About Your Shipment'));
            $html = $comments->toHtml();
        }
        return $html;
    }
}
