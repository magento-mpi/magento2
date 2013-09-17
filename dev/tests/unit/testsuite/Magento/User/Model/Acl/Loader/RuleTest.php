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

class Magento_User_Model_Acl_Loader_RuleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_User_Model_Acl_Loader_Rule
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resourceMock;

    /**
     * @var Magento_Core_Model_Acl_RootResource
     */
    protected $_rootResourceMock;

    protected function setUp()
    {
        $this->_resourceMock = $this->getMock('Magento_Core_Model_Resource', array(), array(), '', false, false);
        $this->_rootResourceMock = new Magento_Core_Model_Acl_RootResource('Magento_Adminhtml::all');
        $this->_model = new Magento_User_Model_Acl_Loader_Rule(
            $this->_rootResourceMock,
            $this->_resourceMock
        );
    }

    public function testPopulateAcl()
    {
        $this->_resourceMock->expects($this->any())->method('getTable')->will($this->returnArgument(1));

        $selectMock = $this->getMock('Magento_DB_Select', array(), array(), '', false);
        $selectMock->expects($this->any())
            ->method('from')
            ->will($this->returnValue($selectMock));

        $adapterMock = $this->getMock('Magento_DB_Adapter_Pdo_Mysql', array(), array(), '', false);
        $adapterMock->expects($this->once())
            ->method('select')
            ->will($this->returnValue($selectMock));
        $adapterMock->expects($this->once())
            ->method('fetchAll')
            ->will($this->returnValue(array(
            array(
                'role_id' => 1,
                'role_type' => 'G',
                'resource_id' => 'Magento_Adminhtml::all',
                'permission' => 'allow'
            ),
            array('role_id' => 2, 'role_type' => 'U', 'resource_id' => 1, 'permission' => 'allow'),
            array('role_id' => 3, 'role_type' => 'U', 'resource_id' => 1, 'permission' => 'deny'),
        )));

        $this->_resourceMock->expects($this->once())
            ->method('getConnection')
            ->will($this->returnValue($adapterMock));

        $aclMock = $this->getMock('Magento_Acl');
        $aclMock->expects($this->any())->method('has')->will($this->returnValue(true));
        $aclMock->expects($this->at(1))->method('allow')->with('G1', null, null);
        $aclMock->expects($this->at(2))->method('allow')->with('G1', 'Magento_Adminhtml::all', null);
        $aclMock->expects($this->at(4))->method('allow')->with('U2', 1, null);
        $aclMock->expects($this->at(6))->method('deny')->with('U3', 1, null);

        $this->_model->populateAcl($aclMock);
    }
}
