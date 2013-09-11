<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Multishipping checkout payment information data
 *
 * @category   Magento
 * @package    Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Checkout\Block\Multishipping\Payment;

class Info extends \Magento\Payment\Block\Info\ContainerAbstract
{
    /**
     * Retrieve payment info model
     *
     * @return \Magento\Payment\Model\Info
     */
    public function getPaymentInfo()
    {
        return \Mage::getSingleton('Magento\Checkout\Model\Type\Multishipping')->getQuote()->getPayment();
    }

    protected function _toHtml()
    {
        $html = '';
        if ($block = $this->getChildBlock($this->_getInfoBlockName())) {
            $html = $block->toHtml();
        }
        return $html;
    }
}
