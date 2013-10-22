<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     \Magento\Stdlib
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Stdlib;

/**
 * Magento methods to work with string
 */
class String
{
    /**
     * Capitalize first letters and convert separators if needed
     *
     * @param string $str
     * @param string $sourceSeparator
     * @param string $destinationSeparator
     * @return string
     */
    public static function upperCaseWords($str, $sourceSeparator = '_', $destinationSeparator = '_')
    {
        return str_replace(' ', $destinationSeparator, ucwords(str_replace($sourceSeparator, ' ', $str)));
    }
}
