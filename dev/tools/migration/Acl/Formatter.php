<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Tools_Migration_Acl_Formatter
{
    /**
     * @param string $string
     * @param string $parameters
     */
    public function parseString($string, $paramenters)
    {
        $tidy = tidy_parse_string($string, $paramenters);
        return $tidy->value;
    }
}
