<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Code standard tool wrapper interface
 */
namespace Magento\TestFramework\CodingStandard;

interface ToolInterface
{
    /**
     * Whether the tool can be ran on the current environment
     *
     * @return bool
     */
    public function canRun();

    /**
     * Run tool for files cpecified
     *
     * @param array $whiteList Files/directories to be inspected
     * @param array $blackList Files/directories to be excluded from the inspection
     * @param array $extensions Array of alphanumeric strings, for example: 'php', 'xml', 'phtml', 'css'...
     *
     * @return int
     */
    public function run(array $whiteList, array $blackList = array(), array $extensions = array());
}
