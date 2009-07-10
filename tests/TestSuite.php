<?php
/**
 * Default Magento TestSuite
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_TestSuite extends PHPUnit_Framework_TestSuite
{
    /**
     * The pattern for TestSuite files
     *
     * @var string
     */
    protected static $_suiteFileMask = '*TestSuite.php';

    /**
     * The pattern for TestCase files
     *
     * @var string
     */
    protected static $_caseFileMask = '*Test.php';

    /**
     * Find TestSuites and TestCases by path and add to Base TestSuite
     *
     * If found TestSuite in path, add TestSuite only
     *
     * @param PHPUnit_Framework_TestSuite $suite
     * @param string $path
     */
    protected static function _findTests(PHPUnit_Framework_TestSuite $suite, $path, $ignoreSuit = false)
    {
        // check exists TestSuite
        $path = rtrim($path, DS) . DS;
        if (!$ignoreSuit) {
            $suits = glob($path . self::$_suiteFileMask);
            if (count($suits) > 0) {
                foreach ($suits as $suitFile) {
                    $suitClassName = basename($suitFile, '.php');
                    include_once $suitFile;
                    $suite->addTestSuite($suitClassName);
                }
                return;
            }
        }

        // processing current directories
        $dirs = glob($path . '*', GLOB_ONLYDIR);
        foreach ($dirs as $dirPath) {
            self::_findTests($suite, $dirPath);
        }

        // find and add test cases
        $cases = glob($path . self::$_caseFileMask);
        $suite->addTestFiles($cases);
    }

    /**
     * Check is Module Enable in Magento
     *
     * @param string $name
     * @return bool
     */
    protected static function _isModuleEnable($name)
    {
        $node = Mage::getConfig()->getNode('modules/' . $name);
        if (!$node) {
            return false;
        }

        if ((string)$node->active != 'true') {
            return false;
        }

        return Mage::helper('core')->isModuleOutputEnabled((string)$node->codePool);
    }
}
