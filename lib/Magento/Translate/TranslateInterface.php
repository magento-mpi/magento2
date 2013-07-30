<?php
/**
 * Basic translation object interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_Translate_TranslateInterface
{
    /**
     * Translates the given string
     * returns the translation
     *
     * @return string
     */
    public function translate();
}
