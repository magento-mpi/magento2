<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento;

/**
 * @todo change this interface when i18n-related logic is moved to library
 */
interface TranslateInterface
{
    /**
     * Default translation string
     */
    const DEFAULT_STRING = 'Translate String';

    /**
     * Initialize translation data
     *
     * @param string|null $area
     * @param bool $forceReload
     * @return \Magento\TranslateInterface
     */
    public function loadData($area = null, $forceReload = false);

    /**
     * Retrieve translation data
     *
     * @return array
     */
    public function getData();

    /**
     * Retrieve locale
     *
     * @return string
     */
    public function getLocale();

    /**
     * Set locale
     *
     * @param string $locale
     * @return \Magento\TranslateInterface
     */
    public function setLocale($locale);

    /**
     * Retrieve theme code
     *
     * @return string
     */
    public function getTheme();
}
