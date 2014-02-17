<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Locale;

interface ConfigInterface
{
    /**
     * Get list pre-configured allowed locales
     *
     * @return array
     */
    public function getAllowedLocales();

    /**
     * Get list pre-configured allowed currencies
     *
     * @return array
     */
    public function getAllowedCurrencies();
}
