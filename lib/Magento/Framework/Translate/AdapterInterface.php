<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Magento translate adapter interface
 */
namespace Magento\Framework\Translate;

interface AdapterInterface
{
    /**
     * Translate string
     *
     * @param string|array $messageId
     * @param null $locale
     * @return string
     */
    public function translate($messageId, $locale = null);

    /**
     * Translate string
     *
     * @return string
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public function __();
}
