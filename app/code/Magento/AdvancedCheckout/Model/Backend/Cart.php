<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
