<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Mage_User_Model_Resource_UserTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_User_Model_Resource_User */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Mage::getResourceSingleton('Mage_User_Model_Resource_User');
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
     * @magentoConfigFixture limitations/admin_account 0
     */
    public function testCanCreateUserZero()
    {
        $this->assertFalse($this->_model->canCreateUser());
    }

    /**
     * Any other values - compare with users count
     *
     * @magentoConfigFixture limitations/admin_account 1
     */
    public function testCanCreateUserFalse()
    {
        $this->assertFalse($this->_model->canCreateUser());
    }

    public function testGetValidationRulesBeforeSave()
    {
        $rules = $this->_model->getValidationRulesBeforeSave();
        $this->assertInstanceOf('Zend_Validate_Interface', $rules);
    }
}
