<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_User_Model_Resource_UserTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_User_Model_Resource_User */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Mage::getResourceSingleton('Mage_User_Model_Resource_User');
    }

    protected function tearDown()
    {
        $this->_model = null;
    }

    /**
     * No node - no limitation
     */
    public function testCanCreateUserTrue()
    {
        $this->assertTrue($this->_model->canCreateUser());
    }

    /**
     * Explicit zero - don't allow creating
     *
     * @magentoConfigFixture global/functional_limitation/max_admin_user_count 0
     */
    public function testCanCreateUserZero()
    {
        $this->assertFalse($this->_model->canCreateUser());
    }

    /**
     * Any other values - compare with users count
     *
     * @magentoConfigFixture global/functional_limitation/max_admin_user_count 1
     */
    public function testCanCreateUserFalse()
    {
        $this->assertFalse($this->_model->canCreateUser());
    }
}
