<?php
/**
 * Translator interface
 *
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
     * Determine if translation is enabled and allowed.
     *
     * @param mixed $store
     * @return bool
     */
    public function isAllowed($store = null);

    /**
     * Initialization translation data
     *
     * @param string $area
     * @param \Magento\Object $initParams
     * @param bool $forceReload
     * @return \Magento\TranslateInterface
     */
    public function init($area = null, $initParams = null, $forceReload = false);

    /**
     * Retrieve active translate mode
     *
     * @return bool
     */
    public function getTranslateInline();

    /**
     * Parse and save edited translate
     *
     * @param array $translate
     */
    public function processAjaxPost($translate);

    /**
     * Replace translation templates with HTML fragments
     *
     * @param array|string $body
     * @param bool $isJson
     */
    public function processResponseBody(&$body, $isJson = false);

    /**
     * Set Translate inline mode
     *
     * @param bool $flag
     * @return \Magento\TranslateInterface
     */
    public function setTranslateInline($flag);

    /**
     * Set locale
     *
     * @param $locale
     * @return \Magento\TranslateInterface
     */
    public function setLocale($locale);

    /**
     * Translate
     *
     * @param array $args
     * @return string
     */
    public function translate($args);
}
