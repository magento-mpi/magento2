<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Locale validator model
 *
 * @category   Magento
 * @package    Magento_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Core_Model_Locale_Validator
{
    /**
     * @var Magento_Core_Model_Locale_Config
     */
    protected $_localeConfig;

    /**
     * Constructor
     *
     * @param Magento_Core_Model_Locale_Config $localeConfig
     */
    public function __construct(Magento_Core_Model_Locale_Config $localeConfig)
    {
        $this->_localeConfig = $localeConfig;
    }

    /**
     * Validate locale code
     *
     * @param string $localeCode
     * @return boolean
     */
    public function isValid($localeCode)
    {
        $isValid = true;
        $allowedLocaleCodes = $this->_localeConfig->getAllowedLocales();

        if (!$localeCode || !in_array($localeCode, $allowedLocaleCodes)) {
            $isValid = false;
        }

        return $isValid;
    }
}
