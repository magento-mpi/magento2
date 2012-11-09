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
            'expectedValue' => 'public',
        ),
        array(
            'name' => '_testPrivate',
            'expectedValue' => 'private',
        ),
        array(
            'name' => '_testPropertyBoolean',
            'expectedValue' => true,
        ),
        array(
            'name' => '_testPropertyInteger',
            'expectedValue' => 10,
        ),
        array(
            'name' => '_testPropertyFloat',
            'expectedValue' => 1.97,
        ),
        array(
            'name' => '_testPropertyString',
            'expectedValue' => 'string',
        ),
        array(
            'name' => '_testPropertyArray',
            'expectedValue' => array('test', 20),
        ),
        array(
            'name' => 'testPublicStatic',
            'expectedValue' => 'static public',
        ),
        array(
            'name' => '_testProtectedStatic',
            'expectedValue' => 'static protected',
        ),
        array(
            'name' => '_testPrivateStatic',
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
        $stubReflection = new ReflectionClass($testClass);
        $this->assertFalse(Magento_Test_ClearProperties_Stub::$isDestructCalled);
        foreach ($this->_properties as $property) {
            $testProperty = $stubReflection->getProperty($property['name']);
            $testProperty->setAccessible(true);
            $actualValue = $testProperty->getValue($testClass);
            $this->assertEquals($property['expectedValue'], $actualValue);
        }
        $clearProperties->endTestSuite($testSuite);
        $this->assertTrue(Magento_Test_ClearProperties_Stub::$isDestructCalled);
        foreach ($this->_properties as $property) {
            $testProperty = $stubReflection->getProperty($property['name']);
            $testProperty->setAccessible(true);
            $actualValue = $testProperty->getValue($testClass);
            $this->assertNull($actualValue);
        }
    }
}

