<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Rma_Block_Order_Info extends Magento_Core_Block_Template
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    public function _construct()
    {
        parent::_construct();

        if (Mage::helper('Magento_Rma_Helper_Data')->isEnabled()) {
            $returns = Mage::getResourceModel('Magento_Rma_Model_Resource_Rma_Grid_Collection')
                ->addFieldToSelect('*')
                ->addFieldToFilter('customer_id', Mage::getSingleton('Magento_Customer_Model_Session')->getCustomer()->getId())
                ->addFieldToFilter('order_id', $this->_coreRegistry->registry('current_order')->getId())
                ->count()
            ;

            if (!empty($returns)) {
                Mage::app()->getLayout()
                    ->getBlock('sales.order.info')
                    ->addLink('returns', 'rma/return/returns', 'Returns');
            }
        }
    }
}
