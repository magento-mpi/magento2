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
 */
class Mage_Webapi_Model_AuthorizationTest extends PHPUnit_Framework_TestCase
{

    /**
     * Test for Mage_Webapi_Model_Authorization::isAllowed()
     *
     * @magentoDataFixture Mage/Webapi/_files/role_with_rule.php
     * @magentoAppIsolation enabled
     */
    public function testIsAllowed()
    {
        /** @var $role Mage_Webapi_Model_Acl_Role */
        $role = Mage::getModel('Mage_Webapi_Model_Acl_Role')->load('Test role', 'role_name');

        //Initialize ACL model
        Mage::getConfig()->setCurrentAreaCode('webapi');
        $aclModel = new Mage_Webapi_Model_Authorization;

        //Test for Allow
        $this->assertTrue($aclModel->isAllowed($role->getRoleId(), 'customer', 'multiGet'));

        //Test for Deny
        $this->assertFalse($aclModel->isAllowed($role->getRoleId(), 'customer', 'delete'));
    }
}
