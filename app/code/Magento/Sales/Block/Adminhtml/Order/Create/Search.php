<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Sales\Block\Adminhtml\Order\Create;

/**
 * Adminhtml sales order create search block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Search extends \Magento\Sales\Block\Adminhtml\Order\Create\AbstractCreate
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_search');
    }

    /**
     * Get header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __('Please select products.');
    }

    /**
     * Get buttons html
     *
     * @return string
     */
    public function getButtonsHtml()
    {
        $addButtonData = [
            'label' => __('Add Selected Product(s) to Order'),
            'onclick' => 'order.productGridAddSelected()',
            'class' => 'action-add',
        ];
        return $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            $addButtonData
        )->toHtml();
    }

    /**
     * Get header css class
     *
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'head-catalog-product';
    }
}
