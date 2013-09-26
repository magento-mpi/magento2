<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * "Returns" link
 */
class Magento_Rma_Block_Order_Link extends Magento_Sales_Block_Order_Link
{
    /**
     * @var Magento_Rma_Helper_Data
     */
    protected $_rmaHelper;

    /**
     * @var Magento_Rma_Model_Resource_Rma_Grid_CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * Constructor
     *
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Rma_Helper_Data $rmaHelper
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Rma_Model_Resource_Rma_Grid_CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Block_Template_Context $context,
        Magento_Rma_Helper_Data $rmaHelper,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Helper_Data $coreData,
        Magento_Rma_Model_Resource_Rma_Grid_CollectionFactory $collectionFactory,
        array $data = array()
    ) {
        $this->_rmaHelper = $rmaHelper;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $registry, $coreData, $data);
    }

    /**
     * @inheritdoc
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_isRmaAviable()) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * Get is link aviable
     * @return bool
     */
    protected function _isRmaAviable()
    {
        if ($this->_rmaHelper->isEnabled()) {
            /** @var $collection Magento_Rma_Model_Resource_Rma_Grid_Collection */
            $collection = $this->_collectionFactory->create();
            $returns = $collection->addFieldToSelect('*')
                ->addFieldToFilter('order_id', $this->_registry->registry('current_order')->getId())
                ->count();

            return $returns > 0;
        } else {
            return false;
        }
    }
}
