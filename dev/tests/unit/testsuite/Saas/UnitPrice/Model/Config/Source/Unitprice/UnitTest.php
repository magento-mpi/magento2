<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_UnitPrice_Model_Config_Source_Unitprice_UnitTest extends PHPUnit_Framework_TestCase
{
    protected function sourceMock(
        Saas_UnitPrice_Helper_Data $helper = null,
        Saas_UnitPrice_Model_Unitprice $unitPrice = null
    ) {
        $source = $this->getMock(
            'Saas_UnitPrice_Model_Config_Source_Unitprice_Unit',
            array('_getHelper', '_getUnitPrice')
        );

        if ($helper) {
            $source->expects($this->any())
                ->method('_getHelper')
                ->will($this->returnValue($helper));
        }

        if ($unitPrice) {
            $source->expects($this->any())
                ->method('_getUnitPrice')
                ->will($this->returnValue($unitPrice));
        }

        return $source;
    }

    protected function helperMock()
    {
        return $this->getMock('Saas_UnitPrice_Helper_Data', array('getConfig', '__'), array(), '', false);
    }

    protected function unitPriceMock()
    {
        return $this->getMock('Saas_UnitPrice_Model_Unitprice', array('toOptionArray'));
    }

    public function testToOptionArrayShouldReturnArrayOfOptionsFromConfig()
    {
        // prepare
        $helper = $this->helperMock();

        $helper->expects($this->once())
            ->method('getConfig')
            ->with('units')
            ->will($this->returnValue('kg,g,m'));

        // @codingStandardsIgnoreStart
        // because Generic.WhiteSpace.ScopeIndent.Incorrect counts spaces incorrectly
        $helper->expects($this->exactly(3))
            ->method('__')
            ->will($this->returnCallback(function ($a) {
                return "local $a";
            }));
        // @codingStandardsIgnoreEnd

        $expectedOptions = array(
            array('value' => 'kg', 'label' => 'local kg'),
            array('value' => 'g',  'label' => 'local g'),
            array('value' => 'm',  'label' => 'local m'),
        );

        $source = $this->sourceMock($helper);

        // act
        $options = $source->toOptionArray();

        // assert
        $this->assertEquals($expectedOptions, $options);
    }

    public function testGetAllOptionsShouldReturnTheSameValueAsToOptionArray()
    {
        // prepare
        $helper = $this->helperMock();

        // @codingStandardsIgnoreStart
        // because Generic.WhiteSpace.ScopeIndent.Incorrect counts spaces incorrectly
        $helper->expects($this->exactly(2))
            ->method('getConfig')
            ->with('units')
            ->will($this->returnValue('kg,g,m'));

        $helper->expects($this->any())
            ->method('__')
            ->will($this->returnCallback(function ($a) {
                return "local $a";
            }));
        // @codingStandardsIgnoreEnd

        $source = $this->sourceMock($helper);

        // act & assert
        $this->assertSame($source->toOptionArray(), $source->getAllOptions());
    }

    public function testGetDefaultValueShouldReturnDefaultValueFromHelper()
    {
        // prepare
        $helper = $this->helperMock();

        $helper->expects($this->once())
            ->method('getConfig')
            ->with('default_unit_price_base_unit')
            ->will($this->returnValue($defaultValue = uniqid()));

        $source = $this->sourceMock($helper);

        // act & assert
        $this->assertEquals($defaultValue, $source->getDefaultValue());
    }

    public function testShouldExtendAbstractSource()
    {
        $this->assertInstanceOf(
            'Magento_Eav_Model_Entity_Attribute_Source_Abstract',
            new Saas_UnitPrice_Model_Config_Source_Unitprice_Unit
        );
    }
}
