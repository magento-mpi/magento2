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
 * Adminhtml sales order create search block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Sales_Order_Create_Search extends Magento_Adminhtml_Block_Sales_Order_Create_Abstract
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_search');
    }

    public function getHeaderText()
    {
        return __('Please select products.');
    }

    public function getButtonsHtml()
    {
        $addButtonData = array(
            'label' => __('Add Selected Product(s) to Order'),
            'onclick' => 'order.productGridAddSelected()',
            'class' => 'action-add',
        );
        return $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')->setData($addButtonData)->toHtml();
    }

    public function getHeaderCssClass()
    {
        return 'head-catalog-product';
    }

}
