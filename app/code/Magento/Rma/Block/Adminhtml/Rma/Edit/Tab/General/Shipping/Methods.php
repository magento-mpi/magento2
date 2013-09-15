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
     * @var \Magento\Tax\Helper\Data
     */
    protected $_taxData = null;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
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
