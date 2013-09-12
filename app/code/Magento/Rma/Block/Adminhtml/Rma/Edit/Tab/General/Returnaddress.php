<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Request Details Block at RMA page
 *
 * @category   Magento
 * @package    Magento_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rma_Block_Adminhtml_Rma_Edit_Tab_General_Returnaddress
    extends Magento_Rma_Block_Adminhtml_Rma_Edit_Tab_General_Abstract
{
    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $data);
    }

    /**
     * Constructor
     */
    public function _construct()
    {
        $order = $this->_coreRegistry->registry('current_order');
        $rma = $this->_coreRegistry->registry('current_rma');
        if ($order && $order->getId()) {
            $this->setStoreId($order->getStoreId());
        } elseif ($rma && $rma->getId()) {
            $this->setStoreId($rma->getStoreId());
        }
    }

    /**
     * Get Customer Email
     *
     * @return string
     */
    public function getReturnAddress()
    {
        return Mage::helper('Magento_Rma_Helper_Data')->getReturnAddress('html', array(), $this->getStoreId());
    }

}
