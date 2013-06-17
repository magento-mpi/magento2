<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Interface for minification adapters
 */
interface Magento_Code_Minifier_AdapterInterface
{
    /**
     * Minify content
     *
     * @param string $content
     * @return string
     */
    public function minify($content);
}
