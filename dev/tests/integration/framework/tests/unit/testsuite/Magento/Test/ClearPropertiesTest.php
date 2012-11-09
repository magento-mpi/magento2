<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Test_ClearProperties.
 */
class Magento_Test_ClearPropertiesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected $_properties = array(
        array(
            'name' => 'testPublic',
            'is_static' => false,
            'expectedValue' => 'public',
        ),
        array(
            'name' => '_testPrivate',
            'is_static' => false,
            'expectedValue' => 'private',
        ),
        array(
            'name' => '_testPropertyBoolean',
            'is_static' => false,
            'expectedValue' => true,
        ),
        array(
            'name' => '_testPropertyInteger',
            'is_static' => false,
            'expectedValue' => 10,
        ),
        array(
            'name' => '_testPropertyFloat',
            'is_static' => false,
            'expectedValue' => 1.97,
        ),
        array(
            'name' => '_testPropertyString',
            'is_static' => false,
            'expectedValue' => 'string',
        ),
        array(
            'name' => '_testPropertyArray',
            'is_static' => false,
            'expectedValue' => array('test', 20),
        ),
        array(
            'name' => 'testPublicStatic',
            'is_static' => true,
            'expectedValue' => 'static public',
        ),
        array(
            'name' => '_testProtectedStatic',
            'is_static' => true,
            'expectedValue' => 'static protected',
        ),
        array(
            'name' => '_testPrivateStatic',
            'is_static' => true,
            'expectedValue' => 'static private',
        ),
    );

    public function testEndTestSuiteDestruct()
    {
        $clearProperties = new Magento_Test_ClearProperties();
        $phpUnitTestSuite = new PHPUnit_Framework_TestSuite();
        $phpUnitTestSuite->addTestFile(__DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR
            . 'DummyTestCase.php'
        );
        // Because addTestFile() adds classes from file to tests array, use first testsuite
        /** @var $testSuite PHPUnit_Framework_TestSuite */
        $testSuite = $phpUnitTestSuite->testAt(0);
        $testSuite->run();
        $testClass = $testSuite->testAt(0);
        foreach ($this->_properties as $property) {
            if ($property['is_static']) {
                $this->assertAttributeEquals($property['expectedValue'], $property['name'], get_class($testClass));
            } else {
                $this->assertAttributeEquals($property['expectedValue'], $property['name'], $testClass);
            }
        }
        $clearProperties->endTestSuite($testSuite);
        $this->assertTrue(Magento_Test_ClearProperties_Stub::$isDestructCalled);
        foreach ($this->_properties as $property) {
            if ($property['is_static']) {
                $this->assertAttributeEmpty($property['name'], get_class($testClass));
            } else {
                $this->assertAttributeEmpty($property['name'], $testClass);
            }
        }
    }
}
