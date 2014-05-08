<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\Tools\Migration\Acl;

class Formatter
{
    /**
     * @param string $string
     * @param string $paramenters
     * @return string
     */
    public function parseString($string, $paramenters)
    {
        $tidy = tidy_parse_string($string, $paramenters);
        return $tidy->value;
    }
}
