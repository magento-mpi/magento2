<?php
/**
* Interface for dependency rule
*
* {license_notice}
*
* @category    tests
* @package     static
* @subpackage  Integrity
* @copyright   {copyright}
* @license     {license_link}
*/

interface Integrity_DependencyTest_RuleInterface
{
    /**
     * Constructor
     */
    public function __construct();

    /**
     * Gets alien dependencies information for current module by analyzing file's contents
     *
     * @param string $currentModule
     * @param string $fileType
     * @param string $file
     * @param string $contents
     * @return array
     */
    public function getDependencyInfo($currentModule, $fileType, $file, &$contents);
}
