<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\AdminGws\Model;

class CallbackBuilder
{
    /**
     * Seek for factory class name in specified callback string
     *
     * @param string $callbackString
     * @return string|array
     */
    public function build($callbackString)
    {
        if (preg_match('/^([^:]+?)::([^:]+?)$/', $callbackString, $matches)) {
            array_shift($matches);
            return $matches;
        }
        return $callbackString;
    }
}
