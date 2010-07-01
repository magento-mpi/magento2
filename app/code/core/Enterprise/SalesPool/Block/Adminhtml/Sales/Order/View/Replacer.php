<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_SalesPool
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Sales pool order view tabs replacer for orders in pool
 *
 */
class Enterprise_SalesPool_Block_Adminhtml_Sales_Order_View_Replacer extends Mage_Adminhtml_Block_Sales_Order_Abstract
{
    /**
     * Disable shipments,invoices and creditmemos tabs for orders which are currently in orders pool
     */
    protected function _prepareLayout()
    {
        if ($this->getOrder()->getInPool()) {
            $hiddenTab = 'enterprise_salespool/adminhtml_sales_order_view_tab_hidden';
            $this->getLayout()->getBlock('sales_order_tabs')->addTab('order_shipments', $hiddenTab);
            $this->getLayout()->getBlock('sales_order_tabs')->addTab('order_invoices', $hiddenTab);
            $this->getLayout()->getBlock('sales_order_tabs')->addTab('order_creditmemos', $hiddenTab);

            $processUrl = $this->getUrl(
                '*/sales_order_pool/flush',
                array('order_id' => $this->getOrder()->getId())
            );

            $this->getLayout()->getBlock('sales_order_edit')->addButton('process',  array(
                'label' => Mage::helper('enterprise_salespool')->__('Process Order'),
                'onclick' => 'setLocation(\'' . $processUrl . '\')',
                'class' => 'delete'
            ));
        }
        return $this;
    }

    protected function _toHtml()
    {
        return '';
    }
}
