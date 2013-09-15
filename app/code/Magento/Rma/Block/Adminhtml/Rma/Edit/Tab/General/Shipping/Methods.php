<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General\Shipping;

class Methods extends \Magento\Core\Block\Template
{
    /**
     * Tax data
     *
     * @var Magento_Tax_Helper_Data
     */
    protected $_taxData = null;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Tax_Helper_Data $taxData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_taxData = $taxData;
        parent::__construct($coreData, $context, $data);
    }

    public function _construct()
    {
        parent::_construct();
        if ($this->_coreRegistry->registry('current_rma')) {
            $this->setShippingMethods($this->_coreRegistry->registry('current_rma')->getShippingMethods());
        }
    }

    public function getShippingPrice($price)
    {
        return $this->_coreRegistry->registry('current_rma')
            ->getStore()
            ->convertPrice(
                $this->_taxData->getShippingPrice(
                    $price
                ),
                true,
                false
            )
        ;
    }

    public function jsonData($method)
    {
        $data = array();
        $data['CarrierTitle']   = $method->getCarrierTitle();
        $data['MethodTitle']    = $method->getMethodTitle();
        $data['Price']          = $this->getShippingPrice($method->getPrice());
        $data['PriceOriginal']  = $method->getPrice();
        $data['Code']           = $method->getCode();

        return $this->_coreData->jsonEncode($data);
    }
}
