<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Webapi
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Webapi_Model_Authorization_Soap_RoleLocatorTest extends PHPUnit_Framework_TestCase
{
    public function testGetAclRoleId()
    {
        $expectedRoleId = 1;
        $userMock = $this->getMockBuilder('Mage_Webapi_Model_Acl_User')
            ->disableOriginalConstructor()
            ->setMethods(array('getRoleId'))
            ->getMock();
        $usernameTokenMock = $this->getMockBuilder('Mage_Webapi_Model_Soap_Security_UsernameToken')
            ->disableOriginalConstructor()
            ->setMethods(array('authenticate'))
            ->getMock();
        $usernameTokenMock->expects($this->once())
            ->method('authenticate')
            ->will($this->returnValue($userMock));

        $userMock->expects($this->once())
            ->method('getRoleId')
            ->will($this->returnValue($expectedRoleId));

        $roleLocator = new Mage_Webapi_Model_Authorization_Soap_RoleLocator(array(
            'usernameToken' => $usernameTokenMock
        ));
        $this->assertEquals($expectedRoleId, $roleLocator->getAclRoleId());
    }

    public function testGetAclRoleIdNoToken()
    {
        $roleLocator = new Mage_Webapi_Model_Authorization_Soap_RoleLocator();
        $this->assertNull($roleLocator->getAclRoleId());
    }
}
