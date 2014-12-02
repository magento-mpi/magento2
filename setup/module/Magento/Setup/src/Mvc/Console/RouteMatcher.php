<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Mvc\Console;

/**
 * Extending ZF RouteMatcher for a public getter
 */
class RouteMatcher extends \Zend\Console\RouteMatcher\DefaultRouteMatcher
{
    /**
     * Public getter of parts, used for parameters validation
     *
     * @return array
     */
    public function getParts()
    {
        return $this->parts;
    }
}
