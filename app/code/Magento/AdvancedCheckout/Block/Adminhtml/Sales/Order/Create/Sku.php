<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdvancedCheckout\Block\Adminhtml\Sales\Order\Create;

/**
 * "Add by SKU" accordion
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Sku extends \Magento\Sales\Block\Adminhtml\Order\Create\AbstractCreate
{
    /**
     * Define ID
     *
     * @return void
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
        $addButtonData = [
            'label' => __('Add to Order'),
            'onclick' => 'addBySku.submitSkuForm()',
            'class' => 'action-add',
        ];
        return $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            $addButtonData
        )->toHtml();
    }
}
