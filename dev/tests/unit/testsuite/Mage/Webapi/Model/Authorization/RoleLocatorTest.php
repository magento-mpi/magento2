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

/**
 * Test class for Mage_Webapi_Model_Authorization_RoleLoactor
 */
class Mage_Webapi_Model_Authorization_RoleLocatorTest extends PHPUnit_Framework_TestCase
{
    public function testGetAclRoleId()
    {
        $expectedRoleId = '557';
        $roleLocator = new Mage_Webapi_Model_Authorization_RoleLocator(array(
            'roleId' => $expectedRoleId
        ));
        $this->assertEquals($expectedRoleId, $roleLocator->getAclRoleId());
    }

    public function testSetRoleId()
    {
        $roleLocator = new Mage_Webapi_Model_Authorization_RoleLocator;
        $expectedRoleId = '557';
        $roleLocator->setRoleId($expectedRoleId);
        $this->assertAttributeEquals($expectedRoleId, '_roleId', $roleLocator);
    }
}
