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
 * Sales archive order view replacer for archive
 *
 */
class Enterprise_SalesArchive_Block_Adminhtml_Sales_Order_View_Replacer
    extends Magento_Adminhtml_Block_Sales_Order_Abstract
{
    protected function _prepareLayout()
    {
        if ($this->getOrder()->getIsArchived()) {
            $this->getLayout()->getBlock('sales_order_tabs')->addTab(
                'enterprise_order_shipments',
                'Enterprise_SalesArchive_Block_Adminhtml_Sales_Order_View_Tab_Shipments'
            );
            $this->getLayout()->getBlock('sales_order_tabs')->addTab(
                'enterprise_order_invoices',
                'Enterprise_SalesArchive_Block_Adminhtml_Sales_Order_View_Tab_Invoices'
            );
            $this->getLayout()->getBlock('sales_order_tabs')->addTab(
                'enterprise_order_creditmemos',
                'Enterprise_SalesArchive_Block_Adminhtml_Sales_Order_View_Tab_Creditmemos'
            );

            $restoreUrl = $this->getUrl(
                '*/sales_archive/remove',
                array('order_id' => $this->getOrder()->getId())
            );
            if ($this->_authorization->isAllowed('Enterprise_SalesArchive::remove')) {
                $this->getLayout()->getBlock('sales_order_edit')->addButton('restore', array(
                    'label' => __('Move to Order Managment'),
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
                if ($this->_authorization->isAllowed('Enterprise_SalesArchive::add')) {
                    $this->getLayout()->getBlock('sales_order_edit')->addButton('restore', array(
                        'label' => __('Move to Archive'),
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
