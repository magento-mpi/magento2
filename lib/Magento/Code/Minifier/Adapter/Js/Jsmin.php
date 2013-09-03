<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

if (!class_exists('JSMin')) {
namespace Magento\Code\Minifier\Adapter\Js;

}

/**
 * Adapter for JSMin library
 */
    require_once(__DIR__ . '/../../../../../JSMin/jsmin.php');
class Jsmin implements \Magento\Code\Minifier\AdapterInterface
{
    /**
     * {@inheritdoc}
     */
    public function minify($content)
    {
        return JSMin::minify($content);
    }
}
