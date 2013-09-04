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
 * Test class for Magento_TestFramework_Workaround_Cleanup_TestCaseProperties.
 */
class Magento_Test_Workaround_Cleanup_TestCasePropertiesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected $_fixtureProperties = array(
        array('name' => 'testPublic', 'is_static' => false),
        array('name' => '_testPrivate', 'is_static' => false),
        array('name' => '_testPropertyBoolean', 'is_static' => false),
        array('name' => '_testPropertyInteger', 'is_static' => false),
        array('name' => '_testPropertyFloat', 'is_static' => false),
        array('name' => '_testPropertyString', 'is_static' => false),
        array('name' => '_testPropertyArray', 'is_static' => false),
        array('name' => '_testPropertyObject', 'is_static' => false),
        array('name' => 'testPublicStatic', 'is_static' => true),
        array('name' => '_testProtectedStatic', 'is_static' => true),
        array('name' => '_testPrivateStatic', 'is_static' => true),
    );

    public function testEndTestSuiteDestruct()
    {
        $phpUnitTestSuite = new PHPUnit_Framework_TestSuite();
        $phpUnitTestSuite->addTestFile(__DIR__ . '/TestCasePropertiesTest/DummyTestCase.php');
        // Because addTestFile() adds classes from file to tests array, use first testsuite
        /** @var $testSuite PHPUnit_Framework_TestSuite */
        $testSuite = $phpUnitTestSuite->testAt(0);
        $testSuite->run();
        /** @var $testClass Magento_Test_Workaround_Cleanup_TestCasePropertiesTest_DummyTestCase */
        $testClass = $testSuite->testAt(0);

        $propertyObjectMock = $this->getMock('stdClass', array('__destruct'));
        $propertyObjectMock
            ->expects($this->once())
            ->method('__destruct');
        $testClass->setPropertyObject($propertyObjectMock);

        foreach ($this->_fixtureProperties as $property) {
            if ($property['is_static']) {
                $this->assertAttributeNotEmpty($property['name'], get_class($testClass));
            } else {
                $this->assertAttributeNotEmpty($property['name'], $testClass);
            }
        }

        $clearProperties = new Magento_TestFramework_Workaround_Cleanup_TestCaseProperties();
        $clearProperties->endTestSuite($testSuite);

        foreach ($this->_fixtureProperties as $property) {
            if ($property['is_static']) {
                $this->assertAttributeEmpty($property['name'], get_class($testClass));
            } else {
                $this->assertAttributeEmpty($property['name'], $testClass);
            }
        }
    }
}
