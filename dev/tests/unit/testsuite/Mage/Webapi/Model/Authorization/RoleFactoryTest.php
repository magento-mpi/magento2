<?php
/**
 * Test class for Mage_Webapi_Model_Authorization_Role_Factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Model_Authorization_Role_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Webapi_Model_Authorization_Role_Factory
     */
    protected $_model;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Mage_Webapi_Model_Authorization_Role
     */
    protected $_expectedObject;

    protected function setUp()
    {
        $helper = new Magento_Test_Helper_ObjectManager($this);

        $this->_objectManager = $this->getMockForAbstractClass('Magento_ObjectManager', array(), '', true, true, true,
            array('create'));

        $this->_expectedObject = $this->getMock('Mage_Webapi_Model_Authorization_Role', array(), array(), '', false);

        $this->_model = $helper->getObject('Mage_Webapi_Model_Authorization_Role_Factory', array(
            'objectManager' => $this->_objectManager,
        ));
    }

    public function testCreateRole()
    {
        $arguments = array('5', '6');

        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with('Mage_Webapi_Model_Authorization_Role', $arguments, false)
            ->will($this->returnValue($this->_expectedObject));
        $this->assertEquals($this->_expectedObject, $this->_model->createRole($arguments));
    }
}
