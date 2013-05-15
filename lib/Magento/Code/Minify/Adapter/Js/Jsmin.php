<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adapter for JSMin library
 */
require_once(__DIR__ . '/../../../../../JSMin/jsmin.php');
class Magento_Code_Minify_Adapter_Js_Jsmin implements Magento_Code_Minify_AdapterInterface
{
    /**
     * {@inheritdoc}
     */
    public function minify($content)
    {
        return JSMin::minify($content);
    }
}
