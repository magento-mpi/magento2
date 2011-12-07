<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales order create items block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Create_Items extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_items');
    }

    public function getHeaderText()
    {
        return Mage::helper('Mage_Sales_Helper_Data')->__('Items Ordered');
    }

    public function getItems()
    {
//        return $this->getQuote()->getAllItems();
        return $this->getQuote()->getAllVisibleItems();
    }

    public function getButtonsHtml()
    {
        $addButtonData = array(
            'label' => Mage::helper('Mage_Sales_Helper_Data')->__('Add Products'),
            'onclick' => "order.productGridShow(this)",
            'class' => 'add',
        );
        return $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')->setData($addButtonData)->toHtml();
    }

    protected function _toHtml()
    {
        if ($this->getStoreId()) {
            return parent::_toHtml();
        }
        return '';
    }

}
