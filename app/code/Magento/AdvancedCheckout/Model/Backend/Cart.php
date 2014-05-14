<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdvancedCheckout\Model\Backend;

/**
 * Backend cart model
 *
 */
class Cart extends \Magento\AdvancedCheckout\Model\Cart
{
    /**
     * Return quote instance for backend area
     *
     * @return \Magento\Backend\Model\Session\Quote|\Magento\Sales\Model\Quote
     */
    public function getActualQuote()
    {
        return $this->_quote->getQuote();
    }
}
