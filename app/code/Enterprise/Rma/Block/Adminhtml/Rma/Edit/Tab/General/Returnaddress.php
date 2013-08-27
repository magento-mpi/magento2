<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Request Details Block at RMA page
 *
 * @category   Enterprise
 * @package    Enterprise_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Rma_Block_Adminhtml_Rma_Edit_Tab_General_Returnaddress
    extends Enterprise_Rma_Block_Adminhtml_Rma_Edit_Tab_General_Abstract
{

    /**
     * Rma data
     *
     * @var Enterprise_Rma_Helper_Data
     */
    protected $_rmaData = null;

    /**
     * @param Enterprise_Rma_Helper_Data $rmaData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Enterprise_Rma_Helper_Data $rmaData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_rmaData = $rmaData;
        parent::__construct($context, $data);
    }

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
        return $this->_rmaData->getReturnAddress('html', array(), $this->getStoreId());
    }

}
