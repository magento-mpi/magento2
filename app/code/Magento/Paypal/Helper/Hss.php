<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Hosted Sole Solution helper
 */
namespace Magento\Paypal\Helper;

class Hss extends \Magento\Core\Helper\AbstractHelper
{
    /**
     * Hosted Sole Solution methods
     *
     * @var array
     */
    protected $_hssMethods = array(
        \Magento\Paypal\Model\Config::METHOD_HOSTEDPRO,
        \Magento\Paypal\Model\Config::METHOD_PAYFLOWLINK,
        \Magento\Paypal\Model\Config::METHOD_PAYFLOWADVANCED
    );

    /**
     * Get template for button in order review page if HSS method was selected
     *
     * @param string $name template name
     * @param string $block buttons block name
     * @return string
     */
    public function getReviewButtonTemplate($name, $block)
    {
        $quote = \Mage::getSingleton('Magento\Checkout\Model\Session')->getQuote();
        if ($quote) {
            $payment = $quote->getPayment();
            if ($payment && in_array($payment->getMethod(), $this->_hssMethods)) {
                return $name;
            }
        }

        if ($blockObject = \Mage::app()->getLayout()->getBlock($block)) {
            return $blockObject->getTemplate();
        }

        return '';
    }

    /**
     * Get methods
     *
     * @return array
     */
    public function getHssMethods()
    {
        return $this->_hssMethods;
    }
}
