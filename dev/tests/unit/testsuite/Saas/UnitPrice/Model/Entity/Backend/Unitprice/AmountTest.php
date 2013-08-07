<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_UnitPrice
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_UnitPrice_Model_Entity_Backend_Unitprice_AmountTest extends PHPUnit_Framework_TestCase
{
    private $_backend;

    protected function setUp()
    {
        $this->_backend = new Saas_UnitPrice_Model_Entity_Backend_Unitprice_Amount;
        $this->_backend->setAttribute(
            new Magento_Object(array('attribute_code' => 'friday', 'default_value' => uniqid()))
        );
    }

    /**
     * @dataProvider providerValue
     */
    public function testBeforeSaveShouldFormatValue($value, $expectedValue)
    {
        // prepare
        $object = new Magento_Object(array('friday' => $value));

        // act
        $this->_backend->beforeSave($object);

        // assert
        $this->assertEquals($expectedValue, $object->getFriday());
    }

    public function providerValue()
    {
        return array(
            array('2.5plus', '2.5'),
            array(.2, '0.2'),
            array(-8e-2, '-0.08'),
        );
    }

    public function testBeforeSaveShouldSetDefaultValueIfObjectHasNoDataForAttribute()
    {
        // prepare
        $object = new Magento_Object;

        // act
        $this->_backend->beforeSave($object);

        // assert
        $this->assertEquals($this->_backend->getDefaultValue(), $object->getFriday());
    }

    public function testBeforeSaveShouldReturnSelf()
    {
        $this->assertSame($this->_backend, $this->_backend->beforeSave(new Magento_Object));
    }
}
