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
     * Translate
     *
     * @param array $args
     * @return string
     */
    public function translate($args);

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
}
