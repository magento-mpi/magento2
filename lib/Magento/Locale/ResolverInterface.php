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
     * Default locale
     */
    const DEFAULT_LOCALE = 'en_US';

    /**
     * Return path to default locale
     *
     * @return string
     */
    public function getDefaultLocalePath();

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
     * @param int $scopeId
     * @return string|null
     */
    public function emulate($scopeId);

    /**
     * Get last locale, used before last emulation
     *
     * @return string|null
     */
    public function revert();
}
