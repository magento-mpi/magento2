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
 * Translate inline interface
 */
interface Mage_Core_Model_Translate_TranslateInterface
{
    /**
     * Is enabled and allowed Inline Translates
     *
     * @return bool
     */
    public function isAllowed();

    /**
     * Parse and save edited translate
     *
     * @param array $translate
     * @return Mage_Core_Model_Translate_TranslateInterface
     */
    public function processAjaxPost($translate);

    /**
     * Replace translation templates with HTML fragments
     *
     * @param array|string $body
     * @return Mage_Core_Model_Translate_TranslateInterface
     */
    public function processResponseBody(&$body);

    /**
     * Set indicator of whether or not content is Json
     *
     * @param bool $flag
     * @return Mage_Core_Model_Translate_TranslateInterface
     */
    public function setIsJson($flag);
}
