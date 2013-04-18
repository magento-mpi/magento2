<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Config model that is aware of all Mage_Paypal payment methods
 * Works with PayPal-specific system configuration
 */
class Saas_Paypal_Model_Config extends Mage_Paypal_Model_Config
{
    /**
     * DE loc
     * @var string
     */
    const LOCALE_DE         = 'DE';

//    /**
//     * Get "What Is PayPal" localized URL
//     * Supposed to be used with "mark" as popup window
//     *
//     * @param Mage_Core_Model_Locale|null $locale
//     * @return string
//     */
//    public function getPaymentMarkWhatIsPaypalUrl(Mage_Core_Model_Locale $locale = null)
//    {
//        return parent::getPaymentMarkWhatIsPaypalUrl($locale);
//    }

    /**
     * Return supported types for PayPal logo
     *
     * @return array
     */
    public function getAdditionalOptionsLogoTypes()
    {
        $merchantCountry = is_null(Mage::app()->getRequest()->getParam('country'))
            ? $this->getMerchantCountry()
            : Mage::app()->getRequest()->getParam('country');

        if ($merchantCountry == self::LOCALE_DE) {
            $logosForDE = array();
            $logoDE = Mage::getConfig()->getFieldset('logo', 'default');
            if (!is_null($logoDE)) {
                foreach ($logoDE as $code => $value) {
                    $logosForDE[$code] = $value;
                }
            }
            return $logosForDE;
        }
        return parent::getAdditionalOptionsLogoTypes();
    }

    /**
     * Return PayPal logo URL with additional options
     *
     * @param string $localeCode Supported locale code
     * @param bool|string $type One of supported logo types
     * @return string|bool Logo Image URL or false if logo disabled in configuration
     */
    public function getAdditionalOptionsLogoUrl($localeCode, $type = false)
    {
        $configType = Mage::getStoreConfig($this->_mapGenericStyleFieldset('logo'), $this->_storeId);
        if (!$configType) {
            return false;
        }
        $type = $type ? $type : $configType;

        return parent::getAdditionalOptionsLogoUrl($localeCode, $type);
    }
}
