<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 *  Add sales archiving to order's grid view massaction
 *
 */
namespace Magento\SalesArchive\Block\Adminhtml\Sales\Order\Grid;

class Button extends \Magento\Adminhtml\Block\Sales\Order\AbstractOrder
{
    protected function _prepareLayout()
    {
        $ordersCount = \Mage::getResourceSingleton('\Magento\SalesArchive\Model\Resource\Order\Collection')->getSize();
        $parent = $this->getLayout()->getBlock('sales_order.grid.container');
        if ($parent && $ordersCount) {
            $url = $this->getUrl('*/sales_archive/orders');
            $parent->addButton('go_to_archive',  array(
                'label'     => __('Go to Archive (%1 orders)', $ordersCount),
                'onclick'   => 'setLocation(\'' . $url . '\')',
                'class'     => 'go'
            ));
        }
        return $this;
    }

    protected function _toHtml()
    {
        return '';
    }
}
