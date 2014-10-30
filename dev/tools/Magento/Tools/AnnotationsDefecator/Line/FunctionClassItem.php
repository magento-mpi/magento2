<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\AnnotationsDefecator\Line;

use Magento\Tools\AnnotationsDefecator\Line;

class FunctionClassItem extends Line
{
    /**
     * Whether content is function element
     *
     * @param string $content
     * @returns bool
     */
    public static function isFunctionClassItem($content)
    {
        $isFunction = preg_match('/^[\s]*(\bfinal \b)?(\bpublic \b|\bprotected \b|\bprivate \b|\bstatic \b)*function ([\w_]+)\((.*)/', $content);
        $isClass = preg_match('/^[\s]*(\bfinal \b)?(\babstract \b)?(\bclass\b|\binterface\b)\s[a-zA-Z_0-9]+/', $content);

        return $isFunction || $isClass;
    }
}
