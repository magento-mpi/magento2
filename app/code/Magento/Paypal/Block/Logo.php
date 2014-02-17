<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * PayPal online logo with additional options
 */
namespace Magento\Paypal\Block;

class Logo extends \Magento\View\Element\Template
{
    /**
     * @var \Magento\Paypal\Model\Config
     */
    protected $_paypalConfig;

    /**
     * @var \Magento\Locale\ResolverInterface
     */
    protected $_localeResolver;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Paypal\Model\Config $paypalConfig
     * @param \Magento\Locale\ResolverInterface $localeResolver
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Paypal\Model\Config $paypalConfig,
        \Magento\Locale\ResolverInterface $localeResolver,
        array $data = array()
    ) {
        $this->_paypalConfig = $paypalConfig;
        $this->_localeResolver = $localeResolver;
        parent::__construct($context, $data);
    }

    /**
     * Return URL for Paypal Landing page
     *
     * @return string
     */
    public function getAboutPaypalPageUrl()
    {
        return $this->_getConfig()->getPaymentMarkWhatIsPaypalUrl($this->_app->getLocaleResolver());
    }

    /**
     * Getter for paypal config
     *
     * @return \Magento\Paypal\Model\Config
     */
    protected function _getConfig()
    {
        return $this->_paypalConfig;
    }

    /**
     * Disable block output if logo turned off
     *M
     * @return string
     */
    protected function _toHtml()
    {
        $type = $this->getLogoType(); // assigned in layout etc.
        $logoUrl = $this->_getConfig()->getAdditionalOptionsLogoUrl(
            $this->_app->getLocaleResolver()->getLocaleCode(), $type
        );
        if (!$logoUrl) {
            return '';
        }
        $this->setLogoImageUrl($logoUrl);
        return parent::_toHtml();
    }
}
