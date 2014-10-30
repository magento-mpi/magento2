<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Tools\AnnotationsDefecator\Line;

use Magento\Tools\AnnotationsDefecator\Line;

class Property extends Line
{
    /**
     * Whether content is property element
     *
     * @param string $content
     * @returns bool
     */
    public static function isProperty($content)
    {
        return preg_match('/^[\s]*(\bprivate\b|\bpublic\b|\bprotected\b)?\s\$[_a-zA-Z0-9]+/', $content);
    }
}