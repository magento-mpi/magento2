<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Selenium_Helper_DataTest extends Mage_PHPUnit_TestCase
{
    /**
     * @covers Mage_Selenium_Helper_Data::getDataValue
     */
    public function testGetDataValue()
    {
        $instance = new Mage_Selenium_Helper_Data($this->_config);
        $this->assertInternalType('array', $instance->getDataValue());
        $this->assertNotEmpty($instance->getDataValue());

        $this->assertFalse($instance->getDataValue('invalid-path'));

        $this->assertArrayHasKey('generic_admin_user', $instance->getDataValue());
        $this->assertInternalType('array', $instance->getDataValue('generic_admin_user'));
        $this->assertInternalType('string', $instance->getDataValue('generic_admin_user/user_name'));
    }

    /**
     * @covers Mage_Selenium_Helper_Data::loadTestDataSet
     */
    public function testLoadTestDataSet()
    {
        $instance = new Mage_Selenium_Helper_Data($this->_config);
        $dataSet = $instance->loadTestDataSet('default\core\Mage\UnitTest\data\UnitTestsData', 'unit_test_load_data');
        $this->assertInternalType('array', $dataSet);
        $this->assertArrayHasKey('another_key', $dataSet);
        $this->assertEquals($dataSet['another_key'], 'another Value');

        $this->assertEquals($dataSet, $instance->loadTestDataSet(
                                                    'default\core\Mage\UnitTest\data\UnitTestsData.yml',
                                                    'unit_test_load_data'));
        $this->assertEquals($dataSet, $instance->loadTestDataSet(
                                                    'default/core/Mage/UnitTest/data/UnitTestsData',
                                                    'unit_test_load_data'));
        $this->assertEquals($dataSet, $instance->loadTestDataSet(
                                                    'default/core/Mage/UnitTest/data/UnitTestsData.yml',
                                                    'unit_test_load_data'));
    }

    /**
     * @covers Mage_Selenium_Helper_Data::loadTestDataSet
     */
    public function testLoadTestDataSetEmpty()
    {
        $instance = new Mage_Selenium_Helper_Data($this->_config);
        $this->setExpectedException('RuntimeException', 'file is empty');
        $instance->loadTestDataSet('default\core\Mage\UnitTest\data\Empty', 'unit_test_load_data');
    }

    /**
     * @covers Mage_Selenium_Helper_Data::loadTestDataSet
     */
    public function testLoadTestDataSetNoDataset()
    {
        $instance = new Mage_Selenium_Helper_Data($this->_config);
        $this->setExpectedException('RuntimeException', 'DataSet with name "not_existing_dataset" is not present');
        $instance->loadTestDataSet('default\core\Mage\UnitTest\data\UnitTestsData', 'not_existing_dataset');
    }
}