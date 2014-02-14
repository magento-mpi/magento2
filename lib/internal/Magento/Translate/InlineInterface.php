<?php
/**
 * Inline translation interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Translate;

interface InlineInterface
{
    /**
     * Returns additional html attribute if needed by client.
     *
     * @param mixed $tagName
     * @return mixed
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
     * @param bool $isJson
     * @return \Magento\Translate\InlineInterface
     */
    public function processResponseBody(&$body, $isJson = false);
}
