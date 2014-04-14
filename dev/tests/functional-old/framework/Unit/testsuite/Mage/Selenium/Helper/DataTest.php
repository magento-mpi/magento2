<?php

/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_Selenium_Helper_DataTest extends Unit_PHPUnit_TestCase
{
    /**
     * @covers Mage_Selenium_Helper_Data::getDataValue
     */
    public function testGetDataValue()
    {
        $instance = new Mage_Selenium_Helper_Data($this->_testConfig);
        $this->assertInternalType('array', $instance->getDataValue());
        $this->assertEmpty($instance->getDataValue());
        $this->assertFalse($instance->getDataValue('invalid-path'));
    }

    /**
     * @covers Mage_Selenium_Helper_Data::loadTestDataSet
     */
    public function testLoadTestDataSet()
    {
        $instance = new Mage_Selenium_Helper_Data($this->_testConfig);
        $dataSet = $instance->loadTestDataSet('default\core\Mage\UnitTest\data\UnitTestsData', 'unit_test_load_data');
        $this->assertInternalType('array', $dataSet);
        $this->assertArrayHasKey('another_key', $dataSet);
        $this->assertEquals($dataSet['another_key'], 'another Value');

        $this->assertEquals(
            $dataSet,
            $instance->loadTestDataSet('default/core/Mage/UnitTest/data/UnitTestsData.yml', 'unit_test_load_data')
        );
        $this->assertEquals(
            $dataSet,
            $instance->loadTestDataSet('default/core/Mage/UnitTest/data/UnitTestsData', 'unit_test_load_data')
        );
        $this->assertEquals(
            $dataSet,
            $instance->loadTestDataSet('default/core/Mage/UnitTest/data/UnitTestsData.yml', 'unit_test_load_data')
        );
    }

    /**
     * @covers Mage_Selenium_Helper_Data::loadTestDataSet
     */
    public function testLoadTestDataSetEmpty()
    {
        $instance = new Mage_Selenium_Helper_Data($this->_testConfig);
        $this->setExpectedException('RuntimeException', 'DataSet with name "unit_test_load_data" is not present in');
        $instance->loadTestDataSet('default/core/Mage/UnitTest/data/Empty', 'unit_test_load_data');
    }

    /**
     * @covers Mage_Selenium_Helper_Data::loadTestDataSet
     */
    public function testLoadTestDataSetNoDataset()
    {
        $instance = new Mage_Selenium_Helper_Data($this->_testConfig);
        $this->setExpectedException('RuntimeException', 'DataSet with name "not_existing_dataset" is not present');
        $instance->loadTestDataSet('default/core/Mage/UnitTest/data/UnitTestsData', 'not_existing_dataset');
    }
}