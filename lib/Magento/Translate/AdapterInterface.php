<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Framework
 * @subpackage  Translate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Magento translate adapter interface
 */
interface Magento_Translate_AdapterInterface
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
     * @param string $messageId
     * @return string
     */
    public function __($messageId);
}
