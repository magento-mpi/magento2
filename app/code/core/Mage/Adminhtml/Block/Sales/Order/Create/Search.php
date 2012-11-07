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
 * Adminhtml sales order create search block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Create_Search extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_search');
    }

    public function getHeaderText()
    {
        return Mage::helper('Mage_Sales_Helper_Data')->__('Please Select Products to Add');
    }

    public function getButtonsHtml()
    {
        $addButtonData = array(
            'label' => Mage::helper('Mage_Sales_Helper_Data')->__('Add Selected Product(s) to Order'),
            'onclick' => 'order.productGridAddSelected()',
            'class' => 'add',
        );
        return $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')->setData($addButtonData)->toHtml();
    }

    public function getHeaderCssClass()
    {
        return 'head-catalog-product';
    }

}
