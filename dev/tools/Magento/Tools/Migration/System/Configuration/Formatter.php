<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Tools\Migration\System\Configuration;

class Formatter
{
    /**
     * @param string $string
     * @param string $parameters
     */
    public function parseString($string, $parameters)
    {
        $tidy = tidy_parse_string($string, $parameters);
        return $tidy->value;
    }
}
