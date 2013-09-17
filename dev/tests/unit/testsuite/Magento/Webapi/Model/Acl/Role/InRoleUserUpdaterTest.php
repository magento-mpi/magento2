<?php
/**
 * Test class for Magento_Webapi_Model_Acl_Role_InRoleUserUpdater.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Acl_Role_InRoleUserUpdaterTest extends PHPUnit_Framework_TestCase
{
    public function testUpdate()
    {
        $roleId = 5;
        $expectedValues = array(7, 8, 9);

        $helper = new Magento_TestFramework_Helper_ObjectManager($this);

        $request = $this->getMockBuilder('Magento_Core_Controller_Request_Http')
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->any())->method('getParam')->will($this->returnValueMap(array(
            array('role_id', null, $roleId)
        )));

        $userResource = $this->getMockBuilder('Magento_Webapi_Model_Resource_Acl_User')
            ->disableOriginalConstructor()
            ->getMock();
        $userResource->expects($this->once())->method('getRoleUsers')
            ->with($roleId)->will($this->returnValue($expectedValues));

        /** @var Magento_Webapi_Model_Acl_Role_InRoleUserUpdater $model */
        $model = $helper->getObject('Magento_Webapi_Model_Acl_Role_InRoleUserUpdater', array(
            'request' => $request,
            'userResource' => $userResource
        ));

        $this->assertEquals($expectedValues, $model->update(array()));
    }
}
