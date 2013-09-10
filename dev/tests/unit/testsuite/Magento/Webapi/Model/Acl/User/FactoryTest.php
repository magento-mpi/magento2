<?php
/**
 * Test class for Magento_Webapi_Model_Acl_User_Factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Acl_User_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Webapi_Model_Acl_User_Factory
     */
    protected $_model;

    /**
     * @var Magento_ObjectManager|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * @var Magento_Webapi_Model_Acl_User
     */
    protected $_expectedObject;

    protected function setUp()
    {
        $helper = new Magento_TestFramework_Helper_ObjectManager($this);

        $this->_objectManager = $this->getMockBuilder('Magento_ObjectManager')
            ->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMockForAbstractClass();

        $this->_expectedObject = $this->getMockBuilder('Magento_Webapi_Model_Acl_User')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_model = $helper->getObject('Magento_Webapi_Model_Acl_User_Factory', array(
            'objectManager' => $this->_objectManager,
        ));
    }

    /**
     * Test create method.
     */
    public function testCreate()
    {
        $arguments = array('property' => 'value');
        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with('Magento_Webapi_Model_Acl_User', $arguments)
            ->will($this->returnValue($this->_expectedObject));
        $this->assertEquals($this->_expectedObject, $this->_model->create($arguments));
    }
}
