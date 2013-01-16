<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_User_Model_Resource_UserTest extends PHPUnit_Framework_TestCase
{
    public function testCanCreateUserTrue()
    {
        /** @var $model Mage_User_Model_Resource_User */
        $model = Mage::getResourceSingleton('Mage_User_Model_Resource_User');
        $this->assertTrue($model->canCreateUser());
    }

    /**
     * @magentoConfigFixture global/functional_limitation/max_admin_user_count 1
     */
    public function testCanCreateUserFalse()
    {
        /** @var $model Mage_User_Model_Resource_User */
        $model = Mage::getResourceSingleton('Mage_User_Model_Resource_User');
        $this->assertFalse($model->canCreateUser());
    }
}
