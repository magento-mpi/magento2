<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     \Magento\Validator
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for \Magento\Validator\Entity\Properties
 */
class Magento_Validator_Entity_PropertiesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Object
     */
    protected $_object;

    protected function setUp()
    {
        $this->_object = $this->getMock('Magento\Object', array('hasDataChanges', 'getData', 'getOrigData'));
    }

    protected function tearDown()
    {
        unset($this->_object);
    }

    /**
     * Testing \Magento\Validator\Entity\Properties::isValid on invalid argument passed
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Instance of \Magento\Object is expected.
     */
    public function testIsValidException()
    {
        $validator = new \Magento\Validator\Entity\Properties();
        $validator->isValid(array());
    }

    /**
     * Testing \Magento\Validator\Entity\Properties::isValid with hasDataChanges and invoked setter
     */
    public function testIsValidSuccessWithInvokedSetter()
    {
        $this->_object->expects($this->once())->method('hasDataChanges')->will($this->returnValue(true));
        $this->_object->expects($this->once())->method('getData')->with('attr1')->will($this->returnValue(1));
        $this->_object->expects($this->once())->method('getOrigData')->with('attr1')->will($this->returnValue(1));

        $validator = new \Magento\Validator\Entity\Properties();
        $validator->setReadOnlyProperties(array('attr1'));
        $this->assertTrue($validator->isValid($this->_object));
    }

    /**
     * Testing \Magento\Validator\Entity\Properties::isValid without invoked setter
     */
    public function testIsValidSuccessWithoutInvokedSetter()
    {
        $validator = new \Magento\Validator\Entity\Properties();
        $this->assertTrue($validator->isValid($this->_object));
    }

    /**
     * Testing \Magento\Validator\Entity\Properties::isValid with unchanged data
     */
    public function testIsValidSuccessWithoutHasDataChanges()
    {
        $this->_object->expects($this->once())->method('hasDataChanges')->will($this->returnValue(false));
        $validator = new \Magento\Validator\Entity\Properties();
        $validator->setReadOnlyProperties(array('attr1'));
        $this->assertTrue($validator->isValid($this->_object));
    }

    /**
     * Testing \Magento\Validator\Entity\Properties::isValid with changed data and invoked setter
     */
    public function testIsValidFailed()
    {
        $this->_object->expects($this->once())->method('hasDataChanges')->will($this->returnValue(true));
        $this->_object->expects($this->once())->method('getData')->with('attr1')->will($this->returnValue(1));
        $this->_object->expects($this->once())->method('getOrigData')->with('attr1')->will($this->returnValue(2));

        $validator = new \Magento\Validator\Entity\Properties();
        $validator->setReadOnlyProperties(array('attr1'));
        $this->assertFalse($validator->isValid($this->_object));
    }
}
