<?php
/**
 * Test class for \Magento\Webapi\Model\Acl\Role\InRoleUserUpdater.
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

        $request = $this->getMockBuilder('Magento\Core\Controller\Request\Http')
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->any())->method('getParam')->will($this->returnValueMap(array(
            array('role_id', null, $roleId)
        )));

        $userResource = $this->getMockBuilder('Magento\Webapi\Model\Resource\Acl\User')
            ->disableOriginalConstructor()
            ->getMock();
        $userResource->expects($this->once())->method('getRoleUsers')
            ->with($roleId)->will($this->returnValue($expectedValues));

        /** @var \Magento\Webapi\Model\Acl\Role\InRoleUserUpdater $model */
        $model = $helper->getObject('\Magento\Webapi\Model\Acl\Role\InRoleUserUpdater', array(
            'request' => $request,
            'userResource' => $userResource
        ));

        $this->assertEquals($expectedValues, $model->update(array()));
    }
}
