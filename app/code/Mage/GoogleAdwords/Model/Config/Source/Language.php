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

    /*
     * Zend Locale language codes corresponding to Google language code
     *
     * @var array
     */
    protected $_googleToZendLanguageCode = array(
        'zh_CN' => 'zh_Hans',
        'zh_TW' => 'zh_Hant',
        'iw' => 'he',
    );

    /**
     * Constructor
     *
     * @param Mage_Core_Model_LocaleInterface $locale
     * @param Mage_GoogleAdwords_Helper_Data $helper
     */
    public function __construct(Mage_Core_Model_LocaleInterface $locale, Mage_GoogleAdwords_Helper_Data $helper)
    {
        $this->_helper = $helper;
        $this->_locale = $locale;
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
        $zendLanguage = $this->_getZendLanguageCode($language);
        $languageLocaleName = $this->_locale->getLocale()->getTranslation($zendLanguage, 'language', $language);
        $languageName = $this->_locale->getLocale()->getTranslation($zendLanguage, 'language');
        if (function_exists('mb_convert_case')) {
            $languageLocaleName = mb_convert_case($languageLocaleName, MB_CASE_TITLE, 'UTF-8');
        } else {
            $languageLocaleName = ucwords($languageLocaleName);
        }
        return sprintf('%s / %s (%s)', $languageLocaleName, $languageName, $language);
    }

    /**
     * Get Zend Locale language code
     *
     * @param string $language
     * @return mixed
     */
    protected function _getZendLanguageCode($language)
    {
        return isset($this->_googleToZendLanguageCode[$language]) ? $this->_googleToZendLanguageCode[$language]
            : $language;
    }
}
