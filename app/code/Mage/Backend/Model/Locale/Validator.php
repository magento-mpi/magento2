<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Locale validator model
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Locale_Validator
{
    /**
     * @var Mage_Core_Model_Locale
     */
    protected $_locale;

    /**
     * Constructor
     *
     * @param Mage_Core_Model_Locale $locale
     */
    public function __construct(
        Mage_Core_Model_Locale $locale
    ) {
        $this->_locale = $locale;
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
        $allowedLocaleCodes = array_keys($this->_locale->getTranslatedOptionLocales());

        if (!$localeCode || !in_array($localeCode, $allowedLocaleCodes)) {
            $isValid = false;
        }

        return $isValid;
    }
}
