<?php
/**
 * Test class for Mage_Webapi_Model_Acl_Role_InRoleUserUpdater
 *
 * @copyright {}
 */
class Mage_Webapi_Model_Acl_Role_InRoleUserUpdaterrTest extends PHPUnit_Framework_TestCase
{
    public function testUpdate()
    {
        $roleId = 5;
        $expectedValues = array(7, 8, 9);

        $helper = new Magento_Test_Helper_ObjectManager($this);

        $request = $this->getMockBuilder('Mage_Core_Controller_Request_Http')
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->any())->method('getParam')->will($this->returnValueMap(array(
            array('role_id', null, $roleId)
        )));

        $userResource = $this->getMockBuilder('Mage_Webapi_Model_Resource_Acl_User')
            ->disableOriginalConstructor()
            ->getMock();
        $userResource->expects($this->once())->method('getRoleUsers')
            ->with($roleId)->will($this->returnValue($expectedValues));

        /** @var Mage_Webapi_Model_Acl_Role_InRoleUserUpdater $model */
        $model = $helper->getModel('Mage_Webapi_Model_Acl_Role_InRoleUserUpdater', array(
            'request' => $request,
            'userResource' => $userResource
        ));

        $this->assertEquals($expectedValues, $model->update(array()));
    }
}
