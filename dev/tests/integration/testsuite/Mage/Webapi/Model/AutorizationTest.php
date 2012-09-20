<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Webapi
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for Mage_Webapi_Model_Authorization
 *
 * @group module:Mage_Webapi
 */
class Mage_Webapi_Model_AuthorizationTest extends PHPUnit_Framework_TestCase
{

    /**
     * Test for Mage_Webapi_Model_Authorization::isAllowed()
     *
     * @magentoDataFixture Mage/Webapi/_files/role_with_rule.php
     */
    public function testIsAllowed()
    {
        $allowResourceId = 'customer/multiGet';
        $denyResourceId = 'customer/delete';
        /** @var $role Mage_Webapi_Model_Acl_Role */
        $role = Mage::getModel('Mage_Webapi_Model_Acl_Role')->load('Test role', 'role_name');

        //Initialize ACL model
        Mage::getConfig()->setCurrentAreaCode('webapi');
        /** @var $aclModel Mage_Webapi_Model_Authorization */
        $aclModel = Mage::getSingleton('Mage_Webapi_Model_Authorization');

        //Test for Allow
        list($resource, $operation) = explode(Mage_Webapi_Model_Authorization::RESOURCE_SEPARATOR, $allowResourceId);
        $this->assertTrue($aclModel->isAllowed($role->getRoleId(), $resource, $operation));

        //Test for Deny
        list($resource, $operation) = explode(Mage_Webapi_Model_Authorization::RESOURCE_SEPARATOR, $denyResourceId);
        $this->assertFalse($aclModel->isAllowed($role->getRoleId(), $resource, $operation));
    }
}
