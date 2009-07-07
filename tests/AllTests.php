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

require_once 'PHPUnit/Framework.php';

require_once 'Mage.php';
require_once 'TestCase.php';
require_once 'TestSuite.php';

AllTests::r

/**
 * Test runner for available UnitTests
 */
class AllTests extends Mage_TestSuite
{
    /**
     * Base Scan folders contains Test
     *
     * @var array
     */
    protected static $_baseTestFolders = array('bugs', 'functional',
        'integration', 'lib','modules', 'selenium', 'webservices');

    /**
     * Retrieve Basic Suite
     *
     * @return Mage_TestSuite
     */
    public static function suite()
    {
        $phpFiles = array();
        foreach (self::$_baseTestFolders as $startingFolder) {
            $phpFiles = array_merge($phpFiles, self::_obtainPhpFiles($startingFolder));
        }

        $suite = new Mage_TestSuite();
        foreach ($phpFiles as $phpFile) {
            if (strpos($phpFile, 'Test.php') !== false) {
                $suite->addTestFile($phpFile);
            }
        }

        return $suite;
    }

    /**
     * Recourse function for fetching all php-files
     *
     * @param string $startPath - start path to search
     * @param array $phpFiles - array where fonded php-files stored
     * @return array list of php-files with UnitTests
     */
    protected static function _obtainPhpFiles($startPath)
    {
        $phpFiles = array();
        if (is_file($startPath)) {
            $extension = pathinfo($startPath, PATHINFO_EXTENSION);
            if ($extension == 'php') {
                $phpFiles[] = $startPath;
            }
        }
        else if (is_dir($startPath)) {
            $dirEntries = scandir($startPath);
            foreach ($dirEntries as $entry) {
                if (strpos($entry, '.') === 0) {
                    continue;
                }
                $path = $startPath . DS . $entry;
                $phpFiles = array_merge($phpFiles, self::_obtainPhpFiles($path));
            }
        }
        else {
            exit("Can't process resource $startPath\n");
        }
        return $phpFiles;
    }

    /**
     * Initialize application
     *
     */
    public static function run()
    {
        $serFileOld = BP . 'app/etc/use_cache.ser';
        $serFileNew = BP . 'app/etc/use_cache.bac';
        $serFileEnv = BP . 'tests/use_cache.ser';

        if (file_exists($serFileOld)) {
            rename($serFileOld, $serFileNew);
        }
        copy($serFileEnv, $serFileOld);

        Mage::app();

        unlink($serFileOld);
        if (file_exists($serFileNew)) {
            rename($serFileNew, $serFileOld);
        }
    }
}
