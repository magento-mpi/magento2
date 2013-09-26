<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Block\Returns;

class Returns extends \Magento\Core\Block\Template
{
    /**
     * Rma data
     *
     * @var \Magento\Rma\Helper\Data
     */
    protected $_rmaData = null;
    
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Rma\Model\Resource\Rma\Grid\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param \Magento\Rma\Helper\Data $rmaData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Rma\Model\Resource\Rma\Grid\CollectionFactory $collectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Rma\Helper\Data $rmaData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Rma\Model\Resource\Rma\Grid\CollectionFactory $collectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        array $data = array()
    ) {
        $this->_rmaData = $rmaData;
        $this->_coreRegistry = $registry;
        $this->_collectionFactory = $collectionFactory;
        $this->_customerSession = $customerSession;
        parent::__construct($coreData, $context, $data);
    }

    public function _construct()
    {
        parent::_construct();
        if ($this->_rmaData->isEnabled()) {
            $this->setTemplate('return/returns.phtml');
            /** @var $returns \Magento\Rma\Model\Resource\Rma\Grid\Collection */
            $returns = $this->_collectionFactory->create()
                ->addFieldToSelect('*')
                ->addFieldToFilter('order_id', $this->_coreRegistry->registry('current_order')->getId())
                ->setOrder('date_requested', 'desc');

            if ($this->_customerSession->isLoggedIn()) {
                $returns->addFieldToFilter('customer_id', $this->_customerSession->getCustomer()->getId());
            }
            $this->setReturns($returns);
        }
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()
            ->createBlock('Magento\Page\Block\Html\Pager', 'sales.order.history.pager')
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
