<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_CustomerBalance_Block_Adminhtml_Customer_Edit_Tab_Customerbalance_Balance_Grid extends
    Magento_Backend_Block_Widget_Grid
{
    /**
     * @var Magento_CustomerBalance_Model_BalanceFactory
     */
    protected $_balanceFactory;

    /**
     * @param Magento_CustomerBalance_Model_BalanceFactory $balanceFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param array $data
     */
    public function __construct(
        Magento_CustomerBalance_Model_BalanceFactory $balanceFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        array $data = array()
    ) {
        $this->_balanceFactory = $balanceFactory;
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
    }

    /**
     * @return Magento_Backend_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = $this->_balanceFactory->create()
            ->getCollection()
            ->addFieldToFilter('customer_id', $this->getRequest()->getParam('id'));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
}
