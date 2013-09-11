<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Block\Return;

class History extends \Magento\Core\Block\Template
{
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('return/history.phtml');

        $returns = \Mage::getResourceModel('\Magento\Rma\Model\Resource\Rma\Grid\Collection')
            ->addFieldToSelect('*')
            ->addFieldToFilter('customer_id', \Mage::getSingleton('Magento\Customer\Model\Session')->getCustomer()->getId())
            ->setOrder('date_requested', 'desc')
        ;


        $this->setReturns($returns);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()
            ->createBlock('\Magento\Page\Block\Html\Pager', 'sales.order.history.pager')
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
        return $this->getUrl('customer/account/');
    }
}
