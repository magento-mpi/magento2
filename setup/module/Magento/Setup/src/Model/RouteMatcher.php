<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Setup\Model;

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
