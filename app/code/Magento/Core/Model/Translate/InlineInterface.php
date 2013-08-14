<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Inline translation interface
 */
interface Magento_Core_Model_Translate_InlineInterface
{
    /**
     * Returns additional html attribute if needed by client.
     *
     * @param mixed|string $tagName
     * @return mixed|string
     */
    public function getAdditionalHtmlAttribute($tagName = null);

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
     * @return Magento_Core_Model_Translate_InlineInterface
     */
    public function processResponseBody(&$body, $isJson);
}
