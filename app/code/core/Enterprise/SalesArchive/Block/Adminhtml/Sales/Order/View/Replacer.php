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
 * @package     Enterprise_SalesArchive
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Sales archive order view replacer for archive
 *
 */
class Enterprise_SalesArchive_Block_Adminhtml_Sales_Order_View_Replacer extends Mage_Adminhtml_Block_Sales_Order_Abstract
{
    protected function _prepareLayout()
    {
        if ($this->getOrder()->getIsArchived()) {
            $this->getLayout()->getBlock('sales_order_tabs')->addTab(
                'order_shipments',
                'Enterprise_SalesArchive_Block_Adminhtml_Sales_Order_View_Tab_Shipments'
            );
            $this->getLayout()->getBlock('sales_order_tabs')->addTab(
                'order_invoices',
                'Enterprise_SalesArchive_Block_Adminhtml_Sales_Order_View_Tab_Invoices'
            );
            $this->getLayout()->getBlock('sales_order_tabs')->addTab(
                'order_creditmemos',
                'Enterprise_SalesArchive_Block_Adminhtml_Sales_Order_View_Tab_Creditmemos'
            );

            $restoreUrl = $this->getUrl(
                '*/sales_archive/remove',
                array('order_id' => $this->getOrder()->getId())
            );
            if (Mage::getSingleton('Mage_Admin_Model_Session')->isAllowed('sales/archive/orders/remove')) {
                $this->getLayout()->getBlock('sales_order_edit')->addButton('restore',  array(
                    'label' => Mage::helper('Enterprise_SalesArchive_Helper_Data')->__('Move to Order Managment'),
                    'onclick' => 'setLocation(\'' . $restoreUrl . '\')',
                    'class' => 'cancel'
                ));
            }
        } elseif ($this->getOrder()->getIsMoveable() !== false) {
            $isActive = Mage::getSingleton('Enterprise_SalesArchive_Model_Config')->isArchiveActive();
            if ($isActive) {
                $archiveUrl = $this->getUrl(
                    '*/sales_archive/add',
                    array('order_id' => $this->getOrder()->getId())
                );
                if (Mage::getSingleton('Mage_Admin_Model_Session')->isAllowed('sales/archive/orders/add')) {
                    $this->getLayout()->getBlock('sales_order_edit')->addButton('restore',  array(
                        'label' => Mage::helper('Enterprise_SalesArchive_Helper_Data')->__('Move to Archive'),
                        'onclick' => 'setLocation(\'' . $archiveUrl . '\')',
                    ));
                }
            }
        }

        return $this;
    }

    protected function _toHtml()
    {
        return '';
    }
}
