<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Tools_Migration_System_Configuration_Formatter
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
