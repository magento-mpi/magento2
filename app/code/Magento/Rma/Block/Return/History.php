<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Rma_Block_Return_History extends Magento_Core_Block_Template
{
    /**
     * @var Magento_Rma_Model_Resource_Rma_Grid_CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Rma_Model_Resource_Rma_Grid_CollectionFactory $collectionFactory
     * @param Magento_Customer_Model_Session $customerSession
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Rma_Model_Resource_Rma_Grid_CollectionFactory $collectionFactory,
        Magento_Customer_Model_Session $customerSession,
        array $data = array()
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_customerSession = $customerSession;
        parent::__construct($coreData, $context, $data);
    }

    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('return/history.phtml');
        /** @var $returns Magento_Rma_Model_Resource_Rma_Grid_Collection */
        $returns = $this->_collectionFactory->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('customer_id', $this->_customerSession->getCustomer()->getId())
            ->setOrder('date_requested', 'desc');
        $this->setReturns($returns);
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
        return $this->getUrl('customer/account/');
    }
}
