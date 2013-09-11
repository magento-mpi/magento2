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
namespace Magento\AdvancedCheckout\Block\Adminhtml\Sales\Order\Create;

class Sku
    extends \Magento\Adminhtml\Block\Sales\Order\Create\AbstractCreate
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
        return $this->getLayout()->createBlock('Magento\Adminhtml\Block\Widget\Button')->setData($addButtonData)->toHtml();
    }
}
