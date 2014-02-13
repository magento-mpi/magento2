<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Helper;

/**
 * Core data helper
 */
class String extends \Magento\App\Helper\AbstractHelper
{
    /**
     * Builds namespace + classname out of the parts array
     *
     * Split every part into pieces by _ and \ and uppercase every piece
     * Then join them back using \
     *
     * @param string[] $parts
     * @return string
     */
    public static function buildClassName($parts)
    {
        $separator = \Magento\Autoload\IncludePath::NS_SEPARATOR;
        $string = join($separator, $parts);
        $string = str_replace('_', $separator, $string);
        $className = str_replace(' ', $separator, ucwords(str_replace($separator, ' ', $string)));
        return $className;
    }
}
