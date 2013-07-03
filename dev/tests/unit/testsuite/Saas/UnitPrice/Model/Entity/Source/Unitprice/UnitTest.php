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
     * @param array $values
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _prepareHelper($values = array())
    {
        /** @var $helper PHPUnit_Framework_MockObject_MockObject */
        $helper = $this->getMock('Saas_UnitPrice_Helper_FakeData', array(), array(), '', false);
        $map = array();
        foreach ($values as $key => $value) {
            $map[] = array($key, $value);
        }

        $helper->expects($this->any())->method('getConfig')->will($this->returnValueMap($map));
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
        $configSourceModel = $this->getMockBuilder('Saas_UnitPrice_Model_Config_Source_Unitprice_Unit')
            ->setMethods(array('toOptionArray'))
            ->getMock();

        $configSourceModel->expects($this->once())
            ->method('toOptionArray')
            ->will($this->returnValue($options));

        return $configSourceModel;
    }

    public function testToOptionsArray()
    {
        $options = array('mm' => 'Milims', 'm' => 'Meters');
        $configSourceModel = $this->_prepareUnitConfigSourceModel($options);

        $this->_source->expects($this->once())
            ->method('_getUnitConfigSourceModel')
            ->will($this->returnValue($configSourceModel));

        // act
        $actualOptions = $this->_source->toOptionArray();

        // assert
        $this->assertEquals($options, $actualOptions);

        // check that delegated method is called once
        $actualOptions = $this->_source->toOptionArray();
    }

    public function testGetAllOptions()
    {
        $options = array('mm' => 'Milims', 'm' => 'Meters');
        $configSourceModel = $this->_prepareUnitConfigSourceModel($options);

        $this->_source->expects($this->once())
            ->method('_getUnitConfigSourceModel')
            ->will($this->returnValue($configSourceModel));

        // act
        $actualOptions = $this->_source->toOptionArray();

        // assert
        $this->assertEquals($options, $actualOptions);

        // check that delegated method is called once
        $actualOptions = $this->_source->getAllOptions();
    }

    public function testGetDefaultValue()
    {
        $this->assertEquals(
            self::DEFAULT_VALUE, $this->_source->getDefaultValue()
        );
    }

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
