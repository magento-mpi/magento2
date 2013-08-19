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
 * One page checkout status
 *
 * @category   Magento
 * @category   Magento
 * @package    Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Checkout_Block_Onepage_Payment extends Magento_Checkout_Block_Onepage_Abstract
{
    protected function _construct()
    {
        $this->getCheckout()->setStepData('payment', array(
            'label'     => __('Payment Information'),
            'is_show'   => $this->isShow()
        ));
        parent::_construct();
    }

    /**
     * Getter
     *
     * @return float
     */
    public function getQuoteBaseGrandTotal()
    {
        return (float)$this->getQuote()->getBaseGrandTotal();
    }
}
