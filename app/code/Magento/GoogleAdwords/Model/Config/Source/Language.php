<?php
/**
 * Google AdWords language source
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\GoogleAdwords\Model\Config\Source;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Language implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Framework\LocaleInterface
     */
    protected $_locale;

    /**
     * @var \Magento\GoogleAdwords\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\GoogleAdwords\Model\Filter\UppercaseTitle
     */
    protected $_uppercaseFilter;

    /**
     * Constructor
     *
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param \Magento\GoogleAdwords\Helper\Data $helper
     * @param \Magento\GoogleAdwords\Model\Filter\UppercaseTitle $uppercaseFilter
     */
    public function __construct(
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\GoogleAdwords\Helper\Data $helper,
        \Magento\GoogleAdwords\Model\Filter\UppercaseTitle $uppercaseFilter
    ) {
        $this->_helper = $helper;
        $this->_locale = $localeResolver->getLocale();
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

            $label = sprintf(
                '%s / %s (%s)',
                $this->_uppercaseFilter->filter($translationForSpecifiedLanguage),
                $translationForDefaultLanguage,
                $languageCode
            );

            $languages[] = array('value' => $languageCode, 'label' => $label);
        }
        return $languages;
    }
}
