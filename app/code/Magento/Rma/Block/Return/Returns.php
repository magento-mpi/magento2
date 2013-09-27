<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Rma_Block_Return_Returns extends Magento_Core_Block_Template
{
    /**
     * Rma data
     *
     * @var Magento_Rma_Helper_Data
     */
    protected $_rmaData = null;
    
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var Magento_Rma_Model_Resource_Rma_Grid_CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * @param Magento_Rma_Model_Resource_Rma_Grid_CollectionFactory $collectionFactory
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Rma_Helper_Data $rmaData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Rma_Model_Resource_Rma_Grid_CollectionFactory $collectionFactory,
        Magento_Customer_Model_Session $customerSession,
        Magento_Rma_Helper_Data $rmaData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
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
            /** @var $returns Magento_Rma_Model_Resource_Rma_Grid_Collection */
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
