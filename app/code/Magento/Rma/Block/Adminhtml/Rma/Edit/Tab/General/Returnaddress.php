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
namespace Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General;

class Returnaddress
    extends \Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General\AbstractGeneral
{

    /**
     * Constructor
     */
    public function _construct()
    {
        if (\Mage::registry('current_order') && \Mage::registry('current_order')->getId()) {
            $this->setStoreId(\Mage::registry('current_order')->getStoreId());
        } elseif (\Mage::registry('current_rma') && \Mage::registry('current_rma')->getId()) {
            $this->setStoreId(\Mage::registry('current_rma')->getStoreId());
        }
    }

    /**
     * Get Customer Email
     *
     * @return string
     */
    public function getReturnAddress()
    {
        return \Mage::helper('Magento\Rma\Helper\Data')->getReturnAddress('html', array(), $this->getStoreId());
    }

}
