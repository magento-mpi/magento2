<?php
/**
 * Test class for Magento_Webapi_Model_Resource_Acl_User
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Resource_Acl_UserTest extends Magento_Webapi_Model_Resource_Acl_TestAbstract
{
    /**
     * Create resource model.
     *
     * @param \Magento\DB\Select $selectMock
     * @return Magento_Webapi_Model_Resource_Acl_User
     */
    protected function _createModel($selectMock = null)
    {
        $this->_resource = $this->getMockBuilder('Magento_Core_Model_Resource')
            ->disableOriginalConstructor()
            ->setMethods(array('getConnection', 'getTableName'))
            ->getMock();

        $this->_resource->expects($this->any())
            ->method('getTableName')
            ->withAnyParameters()
            ->will($this->returnArgument(0));

        $this->_adapter = $this->getMockBuilder('Magento\DB\Adapter\Pdo\Mysql')
            ->disableOriginalConstructor()
            ->setMethods(array('select', 'fetchCol'))
            ->getMock();

        if (!$selectMock) {
            $selectMock = new \Magento\DB\Select(
                $this->getMock('Magento\DB\Adapter\Pdo\Mysql', array(), array(), '', false));
        }

        $this->_adapter->expects($this->any())
            ->method('select')
            ->withAnyParameters()
            ->will($this->returnValue($selectMock));

        $this->_adapter->expects($this->any())
            ->method('fetchCol')
            ->withAnyParameters()
            ->will($this->returnValue(array(1)));

        $this->_resource->expects($this->any())
            ->method('getConnection')
            ->withAnyParameters()
            ->will($this->returnValue($this->_adapter));

        return $this->_helper->getObject('Magento_Webapi_Model_Resource_Acl_User', array(
            'resource' => $this->_resource,
        ));
    }

    /**
     * Test constructor.
     */
    public function testConstructor()
    {
        $model = $this->_createModel();

        $this->assertAttributeEquals('webapi_user', '_mainTable', $model);
        $this->assertAttributeEquals('user_id', '_idFieldName', $model);
    }

    /**
     * Test _initUniqueFields().
     */
    public function testGetUniqueFields()
    {
        $model = $this->_createModel();
        $fields = $model->getUniqueFields();

        $this->assertEquals(array(array('field' => 'api_key', 'title' => 'API Key')), $fields);
    }

    /**
     * Test getRoleUsers().
     */
    public function testGetRoleUsers()
    {
        $selectMock = $this->getMockBuilder('Magento\DB\Select')
            ->setConstructorArgs(array($this->getMock('Magento\DB\Adapter\Pdo\Mysql', array(), array(), '', false)))
            ->setMethods(array('from', 'where'))
            ->getMock();

        $selectMock->expects($this->once())
            ->method('from')
            ->with('webapi_user', array('user_id'))
            ->will($this->returnSelf());

        $selectMock->expects($this->once())
            ->method('where')
            ->with('role_id = ?', 1)
            ->will($this->returnSelf());

        $model = $this->_createModel($selectMock);
        $result = $model->getRoleUsers(1);
        $this->assertEquals(array(1), $result);
    }
}
