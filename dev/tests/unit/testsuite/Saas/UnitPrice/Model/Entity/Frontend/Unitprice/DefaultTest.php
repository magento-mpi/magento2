<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_UnitPrice
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_UnitPrice_Model_Entity_Frontend_Unitprice_DefaultTest
    extends PHPUnit_Framework_TestCase
{
    const DEFAULT_VALUE = 'default_value';

    private $_frontend;

    protected function setUp()
    {
        $attribute = new Magento_Object(
            array(
                'attribute_code' => 'friday',
                'default_value' => self::DEFAULT_VALUE
            )
        );

        $helper = $this->_prepareHelper(
            array(
                'default_' . $attribute->getAttributeCode() => self::DEFAULT_VALUE
            )
        );

        $this->_frontend = $this->getMockBuilder('Saas_UnitPrice_Model_Entity_Frontend_Unitprice_Default')
            ->setMethods(array('_getHelper'))
            ->getMock();

        $this->_frontend->expects($this->any())
            ->method('_getHelper')
            ->will($this->returnValue($helper));

        $this->_frontend->setAttribute($attribute);
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
     * @test
     * @dataProvider providerValue
     */
    public function testGetValue($value, $expectedValue)
    {
        // prepare
        $object = new Magento_Object(array('friday' => $value));

        // act
        $actualValue = $this->_frontend->getValue($object);

        // assert
        $this->assertEquals($expectedValue, $actualValue);
    }

    public function providerValue()
    {
        return array(
            array(null, self::DEFAULT_VALUE),
            array(false, self::DEFAULT_VALUE),
            array(0, self::DEFAULT_VALUE),
            array(1, 1),
            array('1.123 asdasd', '1.123 asdasd')
        );
    }

}
