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
    protected $_gridCollFactory;

    /**
     * Constructor
     *
     * @param Magento_Rma_Model_Resource_Rma_Grid_CollectionFactory $gridCollFactory
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Rma_Helper_Data $rmaHelper
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Helper_Data $coreData
     * @param array $data
     */
    public function __construct(
        Magento_Rma_Model_Resource_Rma_Grid_CollectionFactory $gridCollFactory,
        Magento_Core_Block_Template_Context $context,
        Magento_Rma_Helper_Data $rmaHelper,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Helper_Data $coreData,
        array $data = array()
    ) {
        $this->_gridCollFactory = $gridCollFactory;
        $this->_rmaHelper = $rmaHelper;
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
            $returns = $this->_gridCollFactory
                ->create()
                ->addFieldToSelect('*')
                ->addFieldToFilter('order_id', $this->_registry->registry('current_order')->getId())
                ->count();

            return $returns > 0;
        } else {
            return false;
        }
    }
}
