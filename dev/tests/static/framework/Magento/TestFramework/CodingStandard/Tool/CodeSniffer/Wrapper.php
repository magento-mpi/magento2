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
namespace Magento\TestFramework\CodingStandard\Tool\CodeSniffer;

class Wrapper extends \PHP_CodeSniffer_CLI
{
    /**
     * Emulate console arguments
     *
     * @param $values
     * @return \Magento\TestFramework\CodingStandard\Tool\CodeSniffer\Wrapper
     */
    public function setValues($values)
    {
        $this->values = $values;
        return $this;
    }

    /**
     * Return the current version of php code sniffer
     *
     * @return string
     */
    public function version()
    {
        $version = '0.0.0';
        if (defined('\PHP_CodeSniffer::VERSION')) {
            $phpcs = new \PHP_CodeSniffer();
            $version = $phpcs::VERSION;
        }
        return $version;
    }
}
