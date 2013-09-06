<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Validator
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for Magento_Validator_Entity_Properties
 */
class Magento_Validator_Entity_PropertiesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Object
     */
    protected $_object;

    protected function setUp()
    {
        $this->_object = $this->getMock('Magento_Object', array('hasDataChanges', 'getData', 'getOrigData'));
    }

    protected function tearDown()
    {
        unset($this->_object);
    }

    /**
     * Testing Magento_Validator_Entity_Properties::isValid on invalid argument passed
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Instance of Magento_Object is expected.
     */
    public function testIsValidException()
    {
        $validator = new Magento_Validator_Entity_Properties();
        $validator->isValid(array());
    }

    /**
     * Testing Magento_Validator_Entity_Properties::isValid with hasDataChanges and invoked setter
     */
    public function testIsValidSuccessWithInvokedSetter()
    {
        $this->_object->expects($this->once())->method('hasDataChanges')->will($this->returnValue(true));
        $this->_object->expects($this->once())->method('getData')->with('attr1')->will($this->returnValue(1));
        $this->_object->expects($this->once())->method('getOrigData')->with('attr1')->will($this->returnValue(1));

        $validator = new Magento_Validator_Entity_Properties();
        $validator->setReadOnlyProperties(array('attr1'));
        $this->assertTrue($validator->isValid($this->_object));
    }

    /**
     * Testing Magento_Validator_Entity_Properties::isValid without invoked setter
     */
    public function testIsValidSuccessWithoutInvokedSetter()
    {
        $validator = new Magento_Validator_Entity_Properties();
        $this->assertTrue($validator->isValid($this->_object));
    }

    /**
     * Testing Magento_Validator_Entity_Properties::isValid with unchanged data
     */
    public function testIsValidSuccessWithoutHasDataChanges()
    {
        $this->_object->expects($this->once())->method('hasDataChanges')->will($this->returnValue(false));
        $validator = new Magento_Validator_Entity_Properties();
        $validator->setReadOnlyProperties(array('attr1'));
        $this->assertTrue($validator->isValid($this->_object));
    }

    /**
     * Testing Magento_Validator_Entity_Properties::isValid with changed data and invoked setter
     */
    public function testIsValidFailed()
    {
        $this->_object->expects($this->once())->method('hasDataChanges')->will($this->returnValue(true));
        $this->_object->expects($this->once())->method('getData')->with('attr1')->will($this->returnValue(1));
        $this->_object->expects($this->once())->method('getOrigData')->with('attr1')->will($this->returnValue(2));

        $validator = new Magento_Validator_Entity_Properties();
        $validator->setReadOnlyProperties(array('attr1'));
        $this->assertFalse($validator->isValid($this->_object));
    }
}
