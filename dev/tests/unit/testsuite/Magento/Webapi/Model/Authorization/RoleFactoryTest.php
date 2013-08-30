<?php
/**
 * Test class for Magento_Webapi_Model_Authorization_Role_Factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Authorization_Role_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Webapi_Model_Authorization_Role_Factory
     */
    protected $_model;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Magento_Webapi_Model_Authorization_Role
     */
    protected $_expectedObject;

    protected function setUp()
    {
        $helper = new Magento_TestFramework_Helper_ObjectManager($this);

        $this->_objectManager = $this->getMockForAbstractClass('Magento_ObjectManager', array(), '', true, true, true,
            array('create'));

        $this->_expectedObject = $this->getMock('Magento_Webapi_Model_Authorization_Role', array(), array(), '', false);

        $this->_model = $helper->getObject('Magento_Webapi_Model_Authorization_Role_Factory', array(
            'objectManager' => $this->_objectManager,
        ));
    }

    public function testCreateRole()
    {
        $arguments = array('5', '6');

        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with('Magento_Webapi_Model_Authorization_Role', $arguments)
            ->will($this->returnValue($this->_expectedObject));
        $this->assertEquals($this->_expectedObject, $this->_model->createRole($arguments));
    }
}
