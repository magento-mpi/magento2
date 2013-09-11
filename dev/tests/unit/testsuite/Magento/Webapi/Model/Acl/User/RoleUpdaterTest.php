<?php
/**
 * Test class for \Magento\Webapi\Model\Acl\User\RoleUpdater
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Acl_User_RoleUpdaterTest extends PHPUnit_Framework_TestCase
{
    public function testUpdate()
    {
        $userId = 5;
        $expectedRoleId = 3;

        $helper = new Magento_TestFramework_Helper_ObjectManager($this);

        $request = $this->getMockBuilder('Magento\Core\Controller\Request\Http')
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->any())->method('getParam')->will($this->returnValueMap(array(
            array('user_id', null, $userId)
        )));

        $userModel = $this->getMockBuilder('Magento\Webapi\Model\Acl\User')
            ->setMethods(array('getRoleId', 'load'))
            ->disableOriginalConstructor()
            ->getMock();
        $userModel->expects($this->once())->method('load')
            ->with($userId, null)->will($this->returnSelf());
        $userModel->expects($this->once())->method('getRoleId')
            ->with()->will($this->returnValue($expectedRoleId));

        $userFactory = $this->getMockBuilder('Magento\Webapi\Model\Acl\User\Factory')
            ->setMethods(array('create'))
            ->disableOriginalConstructor()
            ->getMock();
        $userFactory->expects($this->once())->method('create')
            ->with(array())->will($this->returnValue($userModel));

        /** @var \Magento\Webapi\Model\Acl\Role\InRoleUserUpdater $model */
        $model = $helper->getObject('\Magento\Webapi\Model\Acl\User\RoleUpdater', array(
            'request' => $request,
            'userFactory' => $userFactory
        ));

        $this->assertEquals($expectedRoleId, $model->update(array()));
    }
}
