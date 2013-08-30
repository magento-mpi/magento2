<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Sales Order Email Shipment items
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Block_Order_Email_Shipment_Items extends Magento_Sales_Block_Items_Abstract
{
    /**
     * Prepare item before output
     *
     * @param Magento_Core_Block_Abstract $renderer
     * @return Magento_Sales_Block_Items_Abstract
     */
    protected function _prepareItem(Magento_Core_Block_Abstract $renderer)
    {
        $renderer->getItem()->setOrder($this->getOrder());
        $renderer->getItem()->setSource($this->getShipment());
    }
}
