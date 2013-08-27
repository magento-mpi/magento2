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
class Magento_GoogleAdwords_Model_Config_Source_Language implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * @var Zend_Locale
     */
    protected $_locale;

    /**
     * @var Magento_GoogleAdwords_Helper_Data
     */
    protected $_helper;

    /**
     * @var Magento_GoogleAdwords_Model_Filter_UppercaseTitle
     */
    protected $_uppercaseFilter;

    /**
     * Constructor
     *
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_GoogleAdwords_Helper_Data $helper
     * @param Magento_GoogleAdwords_Model_Filter_UppercaseTitle $uppercaseFilter
     */
    public function __construct(
        Magento_Core_Model_LocaleInterface $locale,
        Magento_GoogleAdwords_Helper_Data $helper,
        Magento_GoogleAdwords_Model_Filter_UppercaseTitle $uppercaseFilter
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
