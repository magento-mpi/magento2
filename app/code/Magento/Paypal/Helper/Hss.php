<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Helper;

/**
 * Hosted Sole Solution helper
 */
class Hss extends \Magento\App\Helper\AbstractHelper
{
    /**
     * Hosted Sole Solution methods
     *
     * @var string[]
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
     * @var \Magento\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\View\LayoutInterface $layout
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\View\LayoutInterface $layout
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
     * @return string[]
     */
    public function getHssMethods()
    {
        return $this->_hssMethods;
    }
}
