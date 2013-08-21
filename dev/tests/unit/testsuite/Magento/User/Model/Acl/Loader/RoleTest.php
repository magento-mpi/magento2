<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_User_Model_Acl_Loader_RoleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_User_Model_Acl_Loader_Role
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

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_adapterMock;

    public function setUp()
    {
        $this->_resourceMock = $this->getMock('Magento_Core_Model_Resource', array(), array(), '', false, false);
        $this->_objectFactoryMock = $this->getMock('Magento_Core_Model_Config', array(), array(), '', false);

        $this->_resourceMock->expects($this->once())
            ->method('getTableName')
            ->with($this->equalTo('admin_role'))
            ->will($this->returnArgument(1));


        $selectMock = $this->getMock('Magento_DB_Select', array(), array(), '', false);
        $selectMock->expects($this->any())
            ->method('from')
            ->will($this->returnValue($selectMock));

        $this->_adapterMock = $this->getMock('Magento_DB_Adapter_Pdo_Mysql', array(), array(), '', false);
        $this->_adapterMock->expects($this->once())
            ->method('select')
            ->will($this->returnValue($selectMock));

        $this->_resourceMock->expects($this->once())
            ->method('getConnection')
            ->will($this->returnValue($this->_adapterMock));

        $this->_model = new Magento_User_Model_Acl_Loader_Role(array(
            'resource' => $this->_resourceMock,
            'objectFactory' => $this->_objectFactoryMock
        ));
    }

    public function testPopulateAclAddsRolesAndTheirChildren()
    {
        $this->_adapterMock->expects($this->once())
            ->method('fetchAll')
            ->will($this->returnValue(array(
                array('role_id' => 1, 'role_type' => 'G', 'parent_id' => null),
                array('role_id' => 2, 'role_type' => 'U', 'parent_id' => 1, 'user_id' => 1),
            )));


        $this->_objectFactoryMock->expects($this->at(0))->method('getModelInstance')->with($this->anything(),
            array('roleId' => 'G1')
        );
        $this->_objectFactoryMock->expects($this->at(1))->method('getModelInstance')->with($this->anything(),
            array('roleId' => 'U1')
        );

        $aclMock = $this->getMock('Magento_Acl');
        $aclMock->expects($this->at(0))->method('addRole')->with($this->anything(), null);
        $aclMock->expects($this->at(2))->method('addRole')->with($this->anything(), 'G1');

        $this->_model->populateAcl($aclMock);
    }

    public function testPopulateAclAddsMultipleParents()
    {
        $this->_adapterMock->expects($this->once())
            ->method('fetchAll')
            ->will($this->returnValue(array(
            array('role_id' => 1, 'role_type' => 'U', 'parent_id' => 1, 'user_id' => 1),
        )));

        $this->_objectFactoryMock->expects($this->never())->method('getModelInstance');

        $aclMock = $this->getMock('Magento_Acl');
        $aclMock->expects($this->at(0))->method('hasRole')->with('U1')
            ->will($this->returnValue(true));
        $aclMock->expects($this->at(1))->method('addRoleParent')->with('U1', 'G1');

        $this->_model->populateAcl($aclMock);
    }
}
