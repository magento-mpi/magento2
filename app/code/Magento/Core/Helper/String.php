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
class String extends \Magento\Core\Helper\AbstractHelper
{
    /**
     * @param \Magento\Core\Helper\Context $context
     */
    public function __construct(\Magento\Core\Helper\Context $context)
    {
        parent::__construct($context);
    }

    /**
     * Builds namespace + classname out of the parts array
     *
     * Split every part into pieces by _ and \ and uppercase every piece
     * Then join them back using \
     *
     * @param $parts
     * @return string
     */
    public static function buildClassName($parts)
    {
        $separator = \Magento\Autoload\IncludePath::NS_SEPARATOR;

        $string = join($separator, $parts);
        $string = str_replace('_', $separator, $string);
        $className = \Magento\Stdlib\String::upperCaseWords($string, $separator, $separator);

        return $className;
    }
}
