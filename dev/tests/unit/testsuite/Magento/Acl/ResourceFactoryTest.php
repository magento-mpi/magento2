<?php
/**
 * Test class for Magento_Acl_ResourceFactory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Acl_ResourceFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Acl_ResourceFactory
     */
    protected $_model;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Magento_Acl_Resource
     */
    protected $_expectedObject;

    protected function setUp()
    {
        $helper = new Magento_TestFramework_Helper_ObjectManager($this);

        $this->_objectManager = $this->getMockForAbstractClass('Magento_ObjectManager', array(), '', true, true, true,
            array('create'));

        $this->_expectedObject = $this->getMock('Magento_Acl_Resource', array(), array(), '', false);

        $this->_model = $helper->getObject('Magento_Acl_ResourceFactory', array(
            'objectManager' => $this->_objectManager,
        ));
    }

    public function testCreateResource()
    {
        $arguments = array('5', '6');
        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with('Magento_Acl_Resource', $arguments)
            ->will($this->returnValue($this->_expectedObject));
        $this->assertEquals($this->_expectedObject, $this->_model->createResource($arguments));
    }
}
