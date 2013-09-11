<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Code\Minifier\Adapter\Js;


/**
 * Adapter for JSMin library
 */
class Jsmin implements \Magento\Code\Minifier\AdapterInterface
{
    /**
     * {@inheritdoc}
     */
    public function minify($content)
    {
        if (!class_exists('JSMin')) {
            \Magento\Autoload\IncludePath::load("JSMin/jsmin");
        }
        return \JSMin::minify($content);
    }
}
