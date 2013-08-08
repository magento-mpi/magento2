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
 * Sales Order Email Invoice items
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Block_Order_Email_Invoice_Items extends Mage_Sales_Block_Items_Abstract
{
    /**
     * Prepare item before output
     *
     * @param Magento_Core_Block_Abstract $renderer
     * @return Mage_Sales_Block_Items_Abstract
     */
    protected function _prepareItem(Magento_Core_Block_Abstract $renderer)
    {
        $renderer->getItem()->setOrder($this->getOrder());
        $renderer->getItem()->setSource($this->getInvoice());
    }
}
