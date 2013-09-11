<?php
/**
 * Test class for Magento_Webapi_Model_Authorization_RoleLoactor
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Authorization_RoleLocatorTest extends PHPUnit_Framework_TestCase
{
    public function testGetAclRoleId()
    {
        $expectedRoleId = '557';
        $roleLocator = new \Magento\Webapi\Model\Authorization\RoleLocator(array(
            'roleId' => $expectedRoleId
        ));
        $this->assertEquals($expectedRoleId, $roleLocator->getAclRoleId());
    }

    public function testSetRoleId()
    {
        $roleLocator = new \Magento\Webapi\Model\Authorization\RoleLocator;
        $expectedRoleId = '557';
        $roleLocator->setRoleId($expectedRoleId);
        $this->assertAttributeEquals($expectedRoleId, '_roleId', $roleLocator);
    }
}
