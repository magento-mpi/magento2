<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * "Add by SKU" accordion
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_AdvancedCheckout_Block_Adminhtml_Sales_Order_Create_Sku
    extends Magento_Adminhtml_Block_Sales_Order_Create_Abstract
{
    /**
     * Define ID
     */
    protected function _construct()
    {
        $this->setId('sales_order_create_sku');
    }

    /**
     * Retrieve accordion header
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __('Add to Order by SKU');
    }

    /**
     * Retrieve CSS class for header
     *
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'head-catalog-product';
    }

    /**
     * Retrieve "Add to order" button
     *
     * @return string
     */
    public function getButtonsHtml()
    {
        $addButtonData = array(
            'label' => __('Add to Order'),
            'onclick' => 'addBySku.submitSkuForm()',
            'class' => 'action-add',
        );
        return $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')->setData($addButtonData)->toHtml();
    }
}
