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
     * Constructor
     */
    public function _construct()
    {
        if (Mage::registry('current_order') && Mage::registry('current_order')->getId()) {
            $this->setStoreId(Mage::registry('current_order')->getStoreId());
        } elseif (Mage::registry('current_rma') && Mage::registry('current_rma')->getId()) {
            $this->setStoreId(Mage::registry('current_rma')->getStoreId());
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
