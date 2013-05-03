<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Saas_Model_Tenant_Command_UnlockAdminTest extends PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $resourceModelMock = $this->getMockBuilder('Enterprise_Pci_Model_Resource_Admin_User')
            ->disableOriginalConstructor()
            ->getMock();

        $resourceModelMock
            ->expects($this->once())
            ->method('unlock')
            ->with(1);

        $unlockCommand = new Saas_Saas_Model_Tenant_Command_UnlockAdmin($resourceModelMock);
        $unlockCommand->execute(array('adminId' => 1));
    }
}
