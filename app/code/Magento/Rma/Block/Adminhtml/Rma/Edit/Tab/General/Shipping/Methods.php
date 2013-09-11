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
    public function _construct()
    {
        parent::_construct();
        if (\Mage::registry('current_rma')) {
            $this->setShippingMethods(\Mage::registry('current_rma')->getShippingMethods());
        }
    }

    public function getShippingPrice($price)
    {
        return \Mage::registry('current_rma')
            ->getStore()
            ->convertPrice(
                \Mage::helper('Magento\Tax\Helper\Data')->getShippingPrice(
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

        return \Mage::helper('Magento\Core\Helper\Data')->jsonEncode($data);
    }
}
