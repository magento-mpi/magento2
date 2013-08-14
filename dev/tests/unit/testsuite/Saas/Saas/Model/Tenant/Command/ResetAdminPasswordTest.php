<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Saas_Model_Tenant_Command_ResetAdminPasswordTest extends PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $userModelMock = $this->getMockBuilder('Magento_User_Model_User')
            ->disableOriginalConstructor()
            ->getMock();

        $backendHelperMock = $this->getMockBuilder('Magento_Backend_Helper_Data')
            ->disableOriginalConstructor()
            ->getMock();

        $backendHelperMock
            ->expects($this->once())
            ->method('generateResetPasswordLinkToken')
            ->will($this->returnValue('aaa'));

        $userModelMock
            ->expects($this->once())
            ->method('load')
            ->with(1)
            ->will($this->returnValue($userModelMock));

        $userModelMock
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));

        $userModelMock
            ->expects($this->once())
            ->method('changeResetPasswordLinkToken')
            ->with('aaa')
            ->will($this->returnValue($userModelMock));

        $userModelMock
            ->expects($this->once())
            ->method('save');

        $userModelMock
            ->expects($this->once())
            ->method('sendPasswordResetConfirmationEmail');

        $unlockCommand = new Saas_Saas_Model_Tenant_Command_ResetAdminPassword($userModelMock, $backendHelperMock);
        $unlockCommand->execute(array('adminId' => 1, 'adminEmail' => 'test@test.com'));
    }
}
