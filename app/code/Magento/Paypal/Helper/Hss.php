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
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Core\Model\Layout
     */
    protected $_layout;

    /**
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Core\Model\Layout $layout
     */
    public function __construct(
        \Magento\Core\Helper\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Core\Model\Layout $layout
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_layout = $layout;
        parent::__construct($context);
    }

    /**
     * Get template for button in order review page if HSS method was selected
     *
     * @param string $name template name
     * @param string $block buttons block name
     * @return string
     */
    public function getReviewButtonTemplate($name, $block)
    {
        $quote = $this->_checkoutSession->getQuote();
        if ($quote) {
            $payment = $quote->getPayment();
            if ($payment && in_array($payment->getMethod(), $this->_hssMethods)) {
                return $name;
            }
        }

        $blockObject = $this->_layout->getBlock($block);
        if ($blockObject) {
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
