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
 * @group module:Mage_Webapi
 */
class Mage_Webapi_Model_AuthorizationTest extends PHPUnit_Framework_TestCase
{


    public function testIsAllowed()
    {
        //Initialize data in DB
        $allowResourceId = 'Mage_Customer::customer_multiGet';
        $denyResourceId = 'Mage_Customer::customer_delete';
        $role = new Mage_Webapi_Model_Acl_Role();
        $role->setData(array(
            'role_name' => 'Test role'
        ));
        $role->save();

        $rule = new Mage_Webapi_Model_Acl_Rule();
        $rule->setData(array(
            'resource_id' => $allowResourceId,
            'role_id' => $role->getRoleId()
        ));
        $rule->save();

        //Initialize user session
        $user = new Varien_Object(array(
            'role_id' => $role->getRoleId()
        ));
        Mage::getSingleton('Mage_Core_Model_Session')->setData('webapi_user', $user);

        //Initialize ACL model
        Mage::getConfig()->setCurrentAreaCode('webapi');
        /** @var $aclModel Mage_Webapi_Model_Authorization */
        $aclModel = Mage::getSingleton('Mage_Webapi_Model_Authorization');

        //Test for Allow
        $this->assertTrue($aclModel->isAllowed($allowResourceId));

        //Test for Deny
        $this->assertFalse($aclModel->isAllowed($denyResourceId));
    }
}
