<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Locale;

interface ResolverInterface
{
    /**
     * XML path to the default locale
     */
    const XML_PATH_DEFAULT_LOCALE = 'general/locale/code';

    /**
     * Default locale
     */
    const DEFAULT_LOCALE = 'en_US';

    /**
     * Set default locale code
     *
     * @param   string $locale
     * @return  \Magento\LocaleResolverInterface
     */
    public function setDefaultLocale($locale);

    /**
     * Retrieve default locale code
     *
     * @return string
     */
    public function getDefaultLocale();

    /**
     * Set locale
     *
     * @param   string $locale
     * @return  \Magento\LocaleResolverInterface
     */
    public function setLocale($locale = null);

    /**
     * Retrieve locale object
     *
     * @return \Zend_Locale
     */
    public function getLocale();

    /**
     * Retrieve locale code
     *
     * @return string
     */
    public function getLocaleCode();

    /**
     * Specify current locale code
     *
     * @param   string $code
     * @return  \Magento\LocaleResolverInterface
     */
    public function setLocaleCode($code);
}
