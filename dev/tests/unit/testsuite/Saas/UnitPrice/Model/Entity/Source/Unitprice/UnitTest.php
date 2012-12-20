<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_UnitPrice
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_UnitPrice_Model_Entity_Source_Unitprice_UnitTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * Default attribute value
     */
    const DEFAULT_VALUE = 'default_value';

    /**
     * Reference to source model
     *
     * @var Saas_UnitPrice_Model_Entity_Source_Unitprice_Unit
     */
    private $_source;

    /**
     * Setup source model for testing
     */
    protected function setUp()
    {
        $attribute = new Varien_Object(
            array(
                'attribute_code' => 'friday',
                'default_value' => self::DEFAULT_VALUE
            )
        );

        $this->_source = $this->getMockBuilder('Saas_UnitPrice_Model_Entity_Source_Unitprice_Unit')
            ->setMethods(array('_getHelper', '_getUnitConfigSourceModel'))
            ->getMock();

        $helper = $this->_prepareHelper(
            array('default_' . $attribute->getAttributeCode() => self::DEFAULT_VALUE)
        );

        $this->_source->expects($this->any())
            ->method('_getHelper')
            ->will($this->returnValue($helper));

        $this->_source->setAttribute($attribute);
    }

    /**
     * Prepare unit price fake data helper
     *
     * @param array $value
     * @return Saas_UnitPrice_Helper_FakeData
     */
    protected function _prepareHelper($values = array())
    {
        $helper = new Saas_UnitPrice_Helper_FakeData();
        foreach ($values as $key => $value) {
            $helper->setConfig($key, $value);
        }

        return $helper;
    }

    /**
     * Prepare Unit config source mock
     *
     * @param array $options
     * @return Saas_UnitPrice_Model_Config_Source_Unitprice_Unit
     */
    protected function _prepareUnitConfigSourceModel($options)
    {
        $unitConfigSourceModel = $this->getMockBuilder('Saas_UnitPrice_Model_Config_Source_Unitprice_Unit')
            ->setMethods(array('toOptionArray'))
            ->getMock();

        $unitConfigSourceModel->expects($this->once())
            ->method('toOptionArray')
            ->will($this->returnValue($options));

        return $unitConfigSourceModel;
    }

    /**
     * @test
     */
    public function testToOptionsArray()
    {
        $options = array('mm' => 'Milims', 'm' => 'Meters');
        $unitConfigSourceModel = $this->_prepareUnitConfigSourceModel($options);

        $this->_source->expects($this->once())
            ->method('_getUnitConfigSourceModel')
            ->will($this->returnValue($unitConfigSourceModel));

        // act
        $actualOptions = $this->_source->toOptionArray();

        // assert
        $this->assertEquals($options, $actualOptions);

        // check that delegated method is called once
        $actualOptions = $this->_source->toOptionArray();
    }

    /**
     * @test
     */
    public function testGetAllOptions()
    {
        $options = array('mm' => 'Milims', 'm' => 'Meters');
        $unitConfigSourceModel = $this->_prepareUnitConfigSourceModel($options);

        $this->_source->expects($this->once())
            ->method('_getUnitConfigSourceModel')
            ->will($this->returnValue($unitConfigSourceModel));

        // act
        $actualOptions = $this->_source->toOptionArray();

        // assert
        $this->assertEquals($options, $actualOptions);

        // check that delegated method is called once
        $actualOptions = $this->_source->getAllOptions();
    }

    /**
     * @test
     */
    public function testGetDefaultValue()
    {
        $this->assertEquals(
            self::DEFAULT_VALUE, $this->_source->getDefaultValue()
        );
    }

    /**
     * @test
     */
    public function testGetFlatColumns()
    {
        $expectedColumns = array(
            $this->_source->getAttribute()->getAttributeCode() => array(
                'type'      => 'varchar(255)',
                'unsigned'  => false,
                'is_null'   => true,
                'default'   => self::DEFAULT_VALUE,
                'extra'     => null
            )
        );

        $this->assertEquals($expectedColumns, $this->_source->getFlatColums());
    }
}
