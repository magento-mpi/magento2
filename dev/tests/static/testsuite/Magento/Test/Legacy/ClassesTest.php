<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Integrity
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Scans source code for references to classes and see if they indeed exist
 */
namespace Magento\Test\Legacy;

class ClassesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $file
     * @dataProvider phpCodeDataProvider
     */
    public function testPhpCode($file)
    {
        $classes = \Magento\TestFramework\Utility\Classes::collectPhpCodeClasses(file_get_contents($file));
        $this->_assertNonFactoryName($classes, $file);
    }

    /**
     * @return array
     */
    public function phpCodeDataProvider()
    {
        return \Magento\TestFramework\Utility\Files::init()->getPhpFiles();
    }

    /**
     * @param string $path
     * @dataProvider configFileDataProvider
     */
    public function testConfiguration($path)
    {
        $xml = simplexml_load_file($path);

        $classes = \Magento\TestFramework\Utility\Classes::collectClassesInConfig($xml);
        $this->_assertNonFactoryName($classes, $path);

        $modules = \Magento\TestFramework\Utility\Classes::getXmlAttributeValues($xml, '//@module', 'module');
        $this->_assertNonFactoryName(array_unique($modules), $path, false, true);
    }

    /**
     * @return array
     */
    public function configFileDataProvider()
    {
        return \Magento\TestFramework\Utility\Files::init()->getConfigFiles();
    }

    /**
     * @param string $path
     * @dataProvider layoutFileDataProvider
     */
    public function testLayouts($path)
    {
        $xml = simplexml_load_file($path);
        $classes = \Magento\TestFramework\Utility\Classes::collectLayoutClasses($xml);
        foreach (\Magento\TestFramework\Utility\Classes::getXmlAttributeValues($xml,
            '/layout//@helper', 'helper') as $class) {
            $classes[] = \Magento\TestFramework\Utility\Classes::getCallbackClass($class);
        }
        $classes =
            array_merge($classes, \Magento\TestFramework\Utility\Classes::getXmlAttributeValues($xml,
                    '/layout//@module', 'module'));
        $this->_assertNonFactoryName(array_unique($classes), $path);

        $tabs =
            \Magento\TestFramework\Utility\Classes::getXmlNodeValues($xml, '/layout//action[@method="addTab"]/block');
        $this->_assertNonFactoryName(array_unique($tabs), $path, true);
    }

    /**
     * @return array
     */
    public function layoutFileDataProvider()
    {
        return \Magento\TestFramework\Utility\Files::init()->getLayoutFiles();
    }

    /**
     * Check whether specified classes or module names correspond to a file according PSR-0 standard
     *
     * Suppressing "unused variable" because of the "catch" block
     *
     * @param array $names
     * @param bool $softComparison
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function _assertNonFactoryName($names, $file, $softComparison = false, $moduleBlock = false)
    {
        if (!$names) {
            return;
        }
        $factoryNames = array();
        foreach ($names as $name) {
            try {
                if ($softComparison) {
                    $this->assertNotRegExp('/\//', $name);
                } elseif ($moduleBlock) {
                    $this->assertFalse(false === strpos($name, '_'));
                    $this->assertRegExp('/^([A-Z][A-Za-z\d_]+)+$/', $name);
                } else {
                    if (strpos($name, 'Magento') === false) {
                        continue;
                    }
                    $this->assertFalse(false === strpos($name, '\\'));
                    $this->assertRegExp('/^([A-Z\\\\][A-Za-z\d\\\\]+)+$/', $name);
                }
            } catch (\PHPUnit_Framework_AssertionFailedError $e) {
                $factoryNames[] = $name;
            }
        }
        if ($factoryNames) {
            $this->fail("Obsolete factory name(s) detected in $file:" . "\n" . implode("\n", $factoryNames));
        }
    }
}
