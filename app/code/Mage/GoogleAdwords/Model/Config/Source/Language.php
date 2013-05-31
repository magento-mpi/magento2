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
     * @var Mage_Core_Model_LocaleInterface
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
        $this->_locale = $locale;
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
        foreach ($this->_helper->getLanguageCodes() as $language) {
            $languages[] = array(
                'value' => $language,
                'label' => $this->_getLanguageLabel($language)
            );
        }
        return $languages;
    }

    /**
     * Get language label
     *
     * @param string $language
     * @return string
     */
    protected function _getLanguageLabel($language)
    {
        $currentLanguage = $this->_helper->convertLanguageToCurrentLocale($language);
        $languageLocaleName = $this->_uppercaseFilter->filter(
            $this->_locale->getLocale()->getTranslation($currentLanguage, 'language', $language)
        );
        $languageName = $this->_locale->getLocale()->getTranslation($currentLanguage, 'language');
        return sprintf('%s / %s (%s)', $languageLocaleName, $languageName, $language);
    }
}
