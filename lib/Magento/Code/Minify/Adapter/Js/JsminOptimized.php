<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adapter for optimized JSMin minification library. Requires 'jsmin' PHP extension
 */
class Magento_Code_Minify_Adapter_Js_JsminOptimized implements Magento_Code_Minify_AdapterInterface
{
    /**
     * Check dependencies
     */
    public function __construct()
    {
        if (!class_exists('JSMin')) {
            throw new Magento_Code_Minify_Exception("'JSMin' extension is required");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function minify($content)
    {
        return JSMin::minify($content);
    }
}
