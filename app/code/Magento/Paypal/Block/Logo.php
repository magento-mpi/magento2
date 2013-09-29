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
class Magento_Paypal_Block_Logo extends Magento_Core_Block_Template
{
    /**
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * @var Magento_Paypal_Model_Config
     */
    protected $_paypalConfig;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Paypal_Model_Config $paypalConfig
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Paypal_Model_Config $paypalConfig,
        array $data = array()
    ) {
        $this->_locale = $locale;
        $this->_paypalConfig = $paypalConfig;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Return URL for Paypal Landing page
     *
     * @return string
     */
    public function getAboutPaypalPageUrl()
    {
        return $this->_getConfig()->getPaymentMarkWhatIsPaypalUrl($this->_locale);
    }

    /**
     * Getter for paypal config
     *
     * @return Magento_Paypal_Model_Config
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
        $logoUrl = $this->_getConfig()->getAdditionalOptionsLogoUrl($this->_locale->getLocaleCode(), $type);
        if (!$logoUrl) {
            return '';
        }
        $this->setLogoImageUrl($logoUrl);
        return parent::_toHtml();
    }
}
