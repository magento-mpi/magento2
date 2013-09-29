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
 * Checkout observer model
 *
 * @category   Magento
 * @package    Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Checkout\Model;

class Observer
{
    public function unsetAll()
    {
        \Mage::getSingleton('Magento\Checkout\Model\Session')->unsetAll();
    }

    public function loadCustomerQuote()
    {
        try {
            \Mage::getSingleton('Magento\Checkout\Model\Session')->loadCustomerQuote();
        }
        catch (\Magento\Core\Exception $e) {
            \Mage::getSingleton('Magento\Checkout\Model\Session')->addError($e->getMessage());
        }
        catch (\Exception $e) {
            \Mage::getSingleton('Magento\Checkout\Model\Session')->addException(
                $e,
                __('Load customer quote error')
            );
        }
    }

    public function salesQuoteSaveAfter($observer)
    {
        $quote = $observer->getEvent()->getQuote();
        /* @var $quote \Magento\Sales\Model\Quote */
        if ($quote->getIsCheckoutCart()) {
            \Mage::getSingleton('Magento\Checkout\Model\Session')->getQuoteId($quote->getId());
        }
    }
}
