<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

require_once 'Mage.php';
Mage::app();

$runner = new TestRunner();
$runner->runTests();

/**
 * Test runner for available UnitTests
 */
class TestRunner
{
	private $skipDirs = array('.', '..', '/.', '/..', '.svn');
	private $defaultTestFolders = array(
	           'Enterprise', 'functional', 'integration', 'Mage', 
	           'magento-connect', 'modules', 'selenium', 'WebService', 'webservices');	

    /**
     * Main entry, run tests 
     *
     */
	public function runTests()
	{
		$startingFolders = $this->processCommandLineArgs();
		$phpFiles = array();
		foreach ($startingFolders as $startingFolder) {
            $phpFiles += $this->obtainPhpFiles($startingFolder);			
		}
		$suite = new PHPUnit_Framework_TestSuite();
		foreach ($phpFiles as $phpFile) {
            $className = $this->getClassNameFromPath($phpFile);
            $classNameEnding = substr($className, strlen($className)-4);
    		if (class_exists($className) && $classNameEnding == 'Test') {
                $suite->addTestSuite($className);
    		}
        }
		PHPUnit_TextUI_TestRunner::run($suite);
	}
	
	/**
	 * Function for processing command-line arguments  
	 *
	 * @return array - list of folders/files from command line in which UnitTests should be searched/runned 
	 */
	private function processCommandLineArgs() 
	{
		global $argv; 
		if (in_array('-h', $argv)) {
			$this->showHelp();
			exit(0);
		}	
		else if (sizeof($argv) == 1) {
			return $this->defaultTestFolders;
		}
		else if (sizeof($argv) > 1) {
			return array_slice($argv, 1);
		} 
		else {
			exit("Script should be launched from command line\n");
		}
	}
	
	/**
	 * Recourse function for fetching all php-files 
	 *
	 * @param string $startPath - start path to search
	 * @param array $phpFiles - array where fonded php-files stored
	 * @return list of php-files with UnitTests
	 */
	private function obtainPhpFiles($startPath, $phpFiles = array()) 
	{
    	if (is_file($startPath)) {
			$filePieces = explode('.', $startPath);
			if ($filePieces[sizeof($filePieces)-1] == 'php') {
                $phpFiles[] = $startPath; 
			}
		}
		else if (is_dir($startPath)) {
            $dirEntries = scandir($startPath);
            foreach ($dirEntries as $entry) {
                if (!in_array($entry, $this->skipDirs)) {
                    $phpFiles += $this->obtainPhpFiles("$startPath/$entry", $phpFiles);                		
                }
			}
		}
		else {
			exit("Can't process resouce $startPath\n");
		}
        return $phpFiles;
	}

	/**
	 * Obtains class stored into UnitTest file 
	 *
	 * @param string $sourcePath - full path to UnitTest file (relatively "test" folder)
	 * @return string - class name stored into file
	 */
	private function getClassNameFromPath($sourcePath)
	{
		$fileNameNoExtension = str_replace('.php', '', $sourcePath);
		$className = str_replace('/', '_', $fileNameNoExtension);
		return $className; 
	}

    /**
    * Prints script usage info
    * 
    */	
    private function showHelp() {
        echo "\nCommand-line script for launching UnitTests
Usage: php TestRunner.php [space-separated list of foders/php-files]

Example: php TestRunner.php Enterprise/Invitation Mage/Core/Model/Email/Template/FilterTest.php
All TestCases inside \"Enterprise/Invitation\" and all it subfolders + \"FilterTest.php\" will be run\n";
    }
	
	
}	
