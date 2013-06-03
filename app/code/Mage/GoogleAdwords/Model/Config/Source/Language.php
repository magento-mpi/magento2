<?php
/**
 * Google AdWords language source
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Mage_GoogleAdwords_Model_Config_Source_Language implements Mage_Core_Model_Option_ArrayInterface
{
    /**
     * @var \Zend_Locale
     */
    protected $_locale;

    /**
     * @var Mage_GoogleAdwords_Helper_Data
     */
    protected $_helper;

    /**
     * @var Mage_GoogleAdwords_Model_Filter_UppercaseTitle
     */
    protected $_uppercaseFilter;

    /**
     * Constructor
     *
     * @param Mage_Core_Model_LocaleInterface $locale
     * @param Mage_GoogleAdwords_Helper_Data $helper
     * @param Mage_GoogleAdwords_Model_Filter_UppercaseTitle $uppercaseFilter
     */
    public function __construct(
        Mage_Core_Model_LocaleInterface $locale,
        Mage_GoogleAdwords_Helper_Data $helper,
        Mage_GoogleAdwords_Model_Filter_UppercaseTitle $uppercaseFilter
    ) {
        $this->_helper = $helper;
        $this->_locale = $locale->getLocale();
        $this->_uppercaseFilter = $uppercaseFilter;
    }

    /**
     * Return option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $languages = array();
        foreach ($this->_helper->getLanguageCodes() as $languageCode) {
            $localeCode = $this->_helper->convertLanguageCodeToLocaleCode($languageCode);
            $translationForSpecifiedLanguage = $this->_locale->getTranslation($localeCode, 'language', $languageCode);
            $translationForDefaultLanguage = $this->_locale->getTranslation($localeCode, 'language');

            $label = sprintf('%s / %s (%s)', $this->_uppercaseFilter->filter($translationForSpecifiedLanguage),
                $translationForDefaultLanguage, $languageCode);

            $languages[] = array(
                'value' => $languageCode,
                'label' => $label,
            );
        }
        return $languages;
    }
}
