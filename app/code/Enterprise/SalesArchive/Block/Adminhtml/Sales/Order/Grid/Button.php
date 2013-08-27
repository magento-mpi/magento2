<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 *  Add sales archiving to order's grid view massaction
 *
 */
class Enterprise_SalesArchive_Block_Adminhtml_Sales_Order_Grid_Button extends Magento_Adminhtml_Block_Sales_Order_Abstract
{
    protected function _prepareLayout()
    {
        $ordersCount = Mage::getResourceSingleton('Enterprise_SalesArchive_Model_Resource_Order_Collection')->getSize();
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
