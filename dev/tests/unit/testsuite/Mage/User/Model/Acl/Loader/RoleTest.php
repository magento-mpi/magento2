<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_User
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_User_Model_Acl_Loader_RoleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_User_Model_Acl_Loader_Role
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resourceMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectFactoryMock;

    public function setUp()
    {
        $this->_resourceMock = $this->getMock('Mage_Core_Model_Resource');
        $this->_objectFactoryMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);

        $this->_model = new Mage_User_Model_Acl_Loader_Role(array(
            'resource' => $this->_resourceMock,
            'objectFactory' => $this->_objectFactoryMock
        ));
    }

    public function testPopulateAcl()
    {
        $this->_resourceMock->expects($this->once())
            ->method('getTableName')
            ->with($this->equalTo('admin_role'))
            ->will($this->returnArgument(1));


        $selectMock = $this->getMock('Varien_Db_Select', array(), array(), '', false);
        $selectMock->expects($this->any())
            ->method('from')
            ->will($this->returnValue($selectMock));

        $adapterMock = $this->getMock('Varien_Db_Adapter_Pdo_Mysql', array(), array(), '', false);
        $adapterMock->expects($this->once())
            ->method('select')
            ->will($this->returnValue($selectMock));
        $adapterMock->expects($this->once())
            ->method('fetchAll')
            ->will($this->returnValue(array(
                array('role_id' => 1, 'role_type' => 'G', 'parent_id' => null),
                array('role_id' => 2, 'role_type' => 'U', 'parent_id' => 1, 'user_id' => 1),
                array('role_id' => 3, 'role_type' => 'U', 'parent_id' => 1, 'user_id' => 1),
            )));

        $this->_resourceMock->expects($this->once())
            ->method('getConnection')
            ->will($this->returnValue($adapterMock));

        $this->_objectFactoryMock->expects($this->at(0))->method('getModelInstance')->with($this->anything(), 'G1');
        $this->_objectFactoryMock->expects($this->at(1))->method('getModelInstance')->with($this->anything(), 'U1');
        $this->_objectFactoryMock->expects($this->at(2))->method('getModelInstance')->with($this->anything(), 'U1');

        $aclMock = $this->getMock('Magento_Acl');
        $aclMock->expects($this->at(0))->method('addRole')->with($this->anything(), null);
        $aclMock->expects($this->at(2))->method('addRole')->with($this->anything(), 'G1');

        $this->_model->populateAcl($aclMock);
    }
}
