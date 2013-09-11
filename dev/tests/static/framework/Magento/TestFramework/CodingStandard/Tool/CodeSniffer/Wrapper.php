<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  static_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * PHP Code Sniffer Cli wrapper
 */
class Magento_TestFramework_CodingStandard_Tool_CodeSniffer_Wrapper extends PHP_CodeSniffer_CLI
{
    /**
     * Emulate console arguments
     *
     * @param $values
     * @return Magento_TestFramework_Inspection_CodeSniffer_Cli_Wrapper
     */
    public function setValues($values)
    {
        $this->values = $values;
        return $this;
    }
}
