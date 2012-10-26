<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Eav
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Eav_Model_AttributeFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Eav_Model_AttributeFactory
     */
    protected $_factory;

    /**
     * @var array
     */
    protected $_arguments = array('test1', 'test2');

    /**
     * @var string
     */
    protected $_className = 'Test_Class';

    protected function setUp()
    {
        /** @var $objectManagerMock Magento_ObjectManager_Zend */
        $objectManagerMock = $this->getMock('Magento_ObjectManager_Zend', array(), array(), '', false);
        $objectManagerMock->expects($this->any())
            ->method('create')
            ->will($this->returnCallback(array($this, 'getModelInstance')));

        $this->_factory = new Mage_Eav_Model_AttributeFactory($objectManagerMock);
    }

    protected function tearDown()
    {
        unset($this->_factory);
    }

    /**
     * @covers Mage_Eav_Model_AttributeFactory::createAttribute
     */
    public function testCreateAttribute()
    {
        $this->assertEquals($this->_className,
            $this->_factory->createAttribute($this->_className, $this->_arguments)
        );
    }

    public function getModelInstance($className, $arguments)
    {
        $this->assertInternalType('array', $arguments);
        $this->assertArrayHasKey('data', $arguments);
        $this->assertEquals($this->_arguments, $arguments['data']);

        return $className;
    }
}
