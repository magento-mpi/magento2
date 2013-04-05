<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Inline translation interface
 */
interface Mage_Core_Model_Translate_InlineInterface
{
    /**
     * Format translation for simple tags
     *
     * @param string $tagHtml
     * @param string  $tagName
     * @param array $trArr
     * @return string
     */
    public function applySimpleTagsFormat($tagHtml, $tagName, $trArr);

    /**
     * Format translation for special tags
     *
     * @param string $tagHtml
     * @param string $tagName
     * @param array $trArr
     * @return string
     */
    public function applySpecialTagsFormat($tagHtml, $tagName, $trArr);

    /**
     * Add data-translate-mode attribute
     *
     * @param string $trAttr
     * @return string
     */
    public function addTranslateAttribute($trAttr);

    /**
     * Returns the html span that contains the data translate attribute
     *
     * @param string $data
     * @param string $text
     * @return string
     */
    public function getDataTranslateSpan($data, $text);

    /**
     * Is enabled and allowed Inline Translates
     *
     * @return bool
     */
    public function isAllowed();

    /**
     * Replace translation templates with HTML fragments
     *
     * @param array|string $body
     * @param mixed|bool $isJson
     * @return Mage_Core_Model_Translate_InlineInterface
     */
    public function processResponseBody(&$body, $isJson);
}
