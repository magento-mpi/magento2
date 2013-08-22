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
 * Adminhtml sales order create newsletter block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Sales_Order_Create_Newsletter extends Magento_Adminhtml_Block_Sales_Order_Create_Abstract
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_newsletter');
    }

    public function getHeaderText()
    {
        return __('Newsletter Subscription');
    }

    public function getHeaderCssClass()
    {
        return 'head-newsletter-list';
    }

    protected function _toHtml()
    {
        return parent::_toHtml();
    }

}
