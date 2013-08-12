<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Rma_Block_Return_Returns extends Magento_Core_Block_Template
{
    public function _construct()
    {
        parent::_construct();
        if (Mage::helper('Enterprise_Rma_Helper_Data')->isEnabled()) {
            $this->setTemplate('return/returns.phtml');

            $returns = Mage::getResourceModel('Enterprise_Rma_Model_Resource_Rma_Grid_Collection')
                ->addFieldToSelect('*')
                ->addFieldToFilter('order_id', Mage::registry('current_order')->getId())
                ->setOrder('date_requested', 'desc');

            $customerSession = Mage::getSingleton('Magento_Customer_Model_Session');
            if ($customerSession->isLoggedIn()) {
                $returns->addFieldToFilter('customer_id', $customerSession->getCustomer()->getId());
            }

            $this->setReturns($returns);
        }
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()
            ->createBlock('Magento_Page_Block_Html_Pager', 'sales.order.history.pager')
            ->setCollection($this->getReturns());
        $this->setChild('pager', $pager);
        $this->getReturns()->load();
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getViewUrl($return)
    {
        return $this->getUrl('*/*/view', array('entity_id' => $return->getId()));
    }

    public function getBackUrl()
    {
        return $this->getUrl('sales/order/history');
    }

    public function getReorderUrl($order)
    {
        return $this->getUrl('sales/order/reorder', array('order_id' => $order->getId()));
    }

    public function getPrintUrl($order)
    {
         return $this->getUrl('sales/guest/print', array('order_id' => $order->getId()));
    }
}
