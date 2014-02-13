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
     * @return  \Magento\Locale\ResolverInterface
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
     * @return  \Magento\Locale\ResolverInterface
     */
    public function setLocale($locale = null);

    /**
     * Retrieve locale object
     *
     * @return \Magento\LocaleInterface
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
     * @return  \Magento\Locale\ResolverInterface
     */
    public function setLocaleCode($code);

    /**
     * Push current locale to stack and replace with locale from specified store
     *
     * @param int $storeId
     */
    public function emulate($storeId);

    /**
     * Get last locale, used before last emulation
     */
    public function revert();
}
