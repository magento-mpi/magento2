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
 * Checkout default helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Checkout\Helper;

class Data extends \Magento\Core\Helper\AbstractHelper
{
    const XML_PATH_GUEST_CHECKOUT = 'checkout/options/guest_checkout';
    const XML_PATH_CUSTOMER_MUST_BE_LOGGED = 'checkout/options/customer_must_be_logged';

    protected $_agreements = null;

    /**
     * Retrieve checkout session model
     *
     * @return \Magento\Checkout\Model\Session
     */
    public function getCheckout()
    {
        return \Mage::getSingleton('Magento\Checkout\Model\Session');
    }

    /**
     * Retrieve checkout quote model object
     *
     * @return \Magento\Sales\Model\Quote
     */
    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }

    public function formatPrice($price)
    {
        return $this->getQuote()->getStore()->formatPrice($price);
    }

    public function convertPrice($price, $format=true)
    {
        return $this->getQuote()->getStore()->convertPrice($price, $format);
    }

    public function getRequiredAgreementIds()
    {
        if (is_null($this->_agreements)) {
            if (!\Mage::getStoreConfigFlag('checkout/options/enable_agreements')) {
                $this->_agreements = array();
            } else {
                $this->_agreements = \Mage::getModel('\Magento\Checkout\Model\Agreement')->getCollection()
                    ->addStoreFilter(\Mage::app()->getStore()->getId())
                    ->addFieldToFilter('is_active', 1)
                    ->getAllIds();
            }
        }
        return $this->_agreements;
    }

    /**
     * Get onepage checkout availability
     *
     * @return bool
     */
    public function canOnepageCheckout()
    {
        return (bool)\Mage::getStoreConfig('checkout/options/onepage_checkout_enabled');
    }

    /**
     * Get sales item (quote item, order item etc) price including tax based on row total and tax amount
     *
     * @param   \Magento\Object $item
     * @return  float
     */
    public function getPriceInclTax($item)
    {
        if ($item->getPriceInclTax()) {
            return $item->getPriceInclTax();
        }
        $qty = ($item->getQty() ? $item->getQty() : ($item->getQtyOrdered() ? $item->getQtyOrdered() : 1));
        $taxAmount = $item->getTaxAmount() + $item->getDiscountTaxCompensation();
        $price = (floatval($qty)) ? ($item->getRowTotal() + $taxAmount)/$qty : 0;
        return \Mage::app()->getStore()->roundPrice($price);
    }

    /**
     * Get sales item (quote item, order item etc) row total price including tax
     *
     * @param   \Magento\Object $item
     * @return  float
     */
    public function getSubtotalInclTax($item)
    {
        if ($item->getRowTotalInclTax()) {
            return $item->getRowTotalInclTax();
        }
        $tax = $item->getTaxAmount() + $item->getDiscountTaxCompensation();
        return $item->getRowTotal() + $tax;
    }

    public function getBasePriceInclTax($item)
    {
        $qty = ($item->getQty() ? $item->getQty() : ($item->getQtyOrdered() ? $item->getQtyOrdered() : 1));
        $taxAmount = $item->getBaseTaxAmount() + $item->getBaseDiscountTaxCompensation();
        $price = (floatval($qty)) ? ($item->getBaseRowTotal() + $taxAmount)/$qty : 0;
        return \Mage::app()->getStore()->roundPrice($price);
    }

    public function getBaseSubtotalInclTax($item)
    {
        $tax = $item->getBaseTaxAmount() + $item->getBaseDiscountTaxCompensation();
        return $item->getBaseRowTotal()+$tax;
    }

    /**
     * Send email id payment was failed
     *
     * @param \Magento\Sales\Model\Quote $checkout
     * @param string $message
     * @param string $checkoutType
     * @return \Magento\Checkout\Helper\Data
     */
    public function sendPaymentFailedEmail($checkout, $message, $checkoutType = 'onepage')
    {
        $translate = \Mage::getSingleton('Magento\Core\Model\Translate');
        /* @var $translate \Magento\Core\Model\Translate */
        $translate->setTranslateInline(false);

        $mailTemplate = \Mage::getModel('\Magento\Core\Model\Email\Template');
        /* @var $mailTemplate \Magento\Core\Model\Email\Template */

        $template = \Mage::getStoreConfig('checkout/payment_failed/template', $checkout->getStoreId());

        $copyTo = $this->_getEmails('checkout/payment_failed/copy_to', $checkout->getStoreId());
        $copyMethod = \Mage::getStoreConfig('checkout/payment_failed/copy_method', $checkout->getStoreId());
        if ($copyTo && $copyMethod == 'bcc') {
            $mailTemplate->addBcc($copyTo);
        }

        $_receiver = \Mage::getStoreConfig('checkout/payment_failed/receiver', $checkout->getStoreId());
        $sendTo = array(
            array(
                'email' => \Mage::getStoreConfig('trans_email/ident_'.$_receiver.'/email', $checkout->getStoreId()),
                'name'  => \Mage::getStoreConfig('trans_email/ident_'.$_receiver.'/name', $checkout->getStoreId())
            )
        );

        if ($copyTo && $copyMethod == 'copy') {
            foreach ($copyTo as $email) {
                $sendTo[] = array(
                    'email' => $email,
                    'name'  => null
                );
            }
        }
        $shippingMethod = '';
        if ($shippingInfo = $checkout->getShippingAddress()->getShippingMethod()) {
            $data = explode('_', $shippingInfo);
            $shippingMethod = $data[0];
        }

        $paymentMethod = '';
        if ($paymentInfo = $checkout->getPayment()) {
            $paymentMethod = $paymentInfo->getMethod();
        }

        $items = '';
        foreach ($checkout->getAllVisibleItems() as $_item) {
            /* @var $_item \Magento\Sales\Model\Quote\Item */
            $items .= $_item->getProduct()->getName() . '  x '. $_item->getQty() . '  '
                    . $checkout->getStoreCurrencyCode() . ' '
                    . $_item->getProduct()->getFinalPrice($_item->getQty()) . "\n";
        }
        $total = $checkout->getStoreCurrencyCode() . ' ' . $checkout->getGrandTotal();

        foreach ($sendTo as $recipient) {
            $mailTemplate->setDesignConfig(array(
                'area' => \Magento\Core\Model\App\Area::AREA_FRONTEND,
                'store' => $checkout->getStoreId()
            ))
                ->sendTransactional(
                    $template,
                    \Mage::getStoreConfig('checkout/payment_failed/identity', $checkout->getStoreId()),
                    $recipient['email'],
                    $recipient['name'],
                    array(
                        'reason' => $message,
                        'checkoutType' => $checkoutType,
                        'dateAndTime' => \Mage::app()->getLocale()->date(),
                        'customer' => $checkout->getCustomerFirstname() . ' ' . $checkout->getCustomerLastname(),
                        'customerEmail' => $checkout->getCustomerEmail(),
                        'billingAddress' => $checkout->getBillingAddress(),
                        'shippingAddress' => $checkout->getShippingAddress(),
                        'shippingMethod' => \Mage::getStoreConfig('carriers/'.$shippingMethod.'/title'),
                        'paymentMethod' => \Mage::getStoreConfig('payment/'.$paymentMethod.'/title'),
                        'items' => nl2br($items),
                        'total' => $total
                    )
                );
        }

        $translate->setTranslateInline(true);

        return $this;
    }

    protected function _getEmails($configPath, $storeId)
    {
        $data = \Mage::getStoreConfig($configPath, $storeId);
        if (!empty($data)) {
            return explode(',', $data);
        }
        return false;
    }

    /**
     * Check if multishipping checkout is available.
     * There should be a valid quote in checkout session. If not, only the config value will be returned.
     *
     * @return bool
     */
    public function isMultishippingCheckoutAvailable()
    {
        $quote = $this->getQuote();
        $isMultiShipping = (bool)(int)\Mage::getStoreConfig('shipping/option/checkout_multiple');
        if ((!$quote) || !$quote->hasItems()) {
            return $isMultiShipping;
        }
        $maximunQty = (int)\Mage::getStoreConfig('shipping/option/checkout_multiple_maximum_qty');
        return $isMultiShipping
            && !$quote->hasItemsWithDecimalQty()
            && $quote->validateMinimumAmount(true)
            && (($quote->getItemsSummaryQty() - $quote->getItemVirtualQty()) > 0)
            && ($quote->getItemsSummaryQty() <= $maximunQty)
            && !$quote->hasNominalItems()
        ;
    }

    /**
     * Check is allowed Guest Checkout
     * Use config settings and observer
     *
     * @param \Magento\Sales\Model\Quote $quote
     * @param int|\Magento\Core\Model\Store $store
     * @return bool
     */
    public function isAllowedGuestCheckout(\Magento\Sales\Model\Quote $quote, $store = null)
    {
        if ($store === null) {
            $store = $quote->getStoreId();
        }
        $guestCheckout = \Mage::getStoreConfigFlag(self::XML_PATH_GUEST_CHECKOUT, $store);

        if ($guestCheckout == true) {
            $result = new \Magento\Object();
            $result->setIsAllowed($guestCheckout);
            \Mage::dispatchEvent('checkout_allow_guest', array(
                'quote'  => $quote,
                'store'  => $store,
                'result' => $result
            ));

            $guestCheckout = $result->getIsAllowed();
        }

        return $guestCheckout;
    }

    /**
     * Check if context is checkout
     *
     * @return bool
     */
    public function isContextCheckout()
    {
        return (\Mage::app()->getRequest()->getParam('context') == 'checkout');
    }

    /**
     * Check if user must be logged during checkout process
     *
     * @return boolean
     */
    public function isCustomerMustBeLogged()
    {
        return \Mage::getStoreConfigFlag(self::XML_PATH_CUSTOMER_MUST_BE_LOGGED);
    }
}
