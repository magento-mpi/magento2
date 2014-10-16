<?php
/**
 * Search client helper interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Solr\Helper;

interface ClientInterface
{
    /**
     * Return search client options
     *
     * @param array $options
     * @return mixed
     */
    public function prepareClientOptions($options = array());

    /**
     * Retrieve supported by Solr languages including locale codes (language codes) that are specified in configuration
     * Array(
     *      'language_code1' => 'locale_code',
     *      'language_code2' => Array('locale_code1', 'locale_code2')
     * )
     *
     * @return array
     */
    public function getSolrSupportedLanguages();

    /**
     * Retrieve language code by specified locale code if this locale is supported
     *
     * @param  string $localeCode
     * @return string|false
     */
    public function getLanguageCodeByLocaleCode($localeCode);

    /**
     * Prepare language suffix for text fields.
     * For not supported languages prefix _def will be returned.
     *
     * @param  string $localeCode
     * @return string
     */
    public function getLanguageSuffix($localeCode);
}
