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

class Mage_User_Model_Acl_Loader_RuleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_User_Model_Acl_Loader_Rule
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resourceMock;

    public function setUp()
    {
        $this->_resourceMock = $this->getMock('Mage_Core_Model_Resource');
        $this->_model = new Mage_User_Model_Acl_Loader_Rule(array(
            'resource' => $this->_resourceMock
        ));
    }

    public function testPopulateAcl()
    {
        $this->_resourceMock->expects($this->any())->method('getTable')->will($this->returnArgument(1));

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
            array('role_id' => 1, 'role_type' => 'G', 'resource_id' => 'Mage_Adminhtml::all', 'permission' => 'allow'),
            array('role_id' => 2, 'role_type' => 'U', 'resource_id' => 1, 'permission' => 'allow'),
            array('role_id' => 3, 'role_type' => 'U', 'resource_id' => 1, 'permission' => 'deny'),
        )));

        $this->_resourceMock->expects($this->once())
            ->method('getConnection')
            ->will($this->returnValue($adapterMock));

        $aclMock = $this->getMock('Magento_Acl');
        $aclMock->expects($this->at(0))->method('allow')->with('G1', null, null);
        $aclMock->expects($this->at(1))->method('allow')->with('G1', 'Mage_Adminhtml::all', null);
        $aclMock->expects($this->at(2))->method('allow')->with('U2', 1, null);
        $aclMock->expects($this->at(3))->method('deny')->with('U3', 1, null);

        $this->_model->populateAcl($aclMock);
    }
}
