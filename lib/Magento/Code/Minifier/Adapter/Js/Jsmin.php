<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

if (!class_exists('JSMin')) {
    require_once(__DIR__ . '/../../../../../JSMin/jsmin.php');
}

/**
 * Adapter for JSMin library
 */
class Magento_Code_Minifier_Adapter_Js_Jsmin implements Magento_Code_Minifier_AdapterInterface
{
    /**
     * {@inheritdoc}
     */
    public function minify($content)
    {
        return JSMin::minify($content);
    }
}
