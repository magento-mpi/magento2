<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Admin
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Admin
 * @magentoDataFixture Mage/Admin/_files/user.php
 */
class Mage_Admin_Model_UserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Admin_Model_User
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Mage_Admin_Model_User;
    }

    /**
     * Ensure that an exception is not thrown, if the user does not exist
     */
    public function testLoadByUsername()
    {
        $this->_model->loadByUsername('non_existing_user');
        $this->assertNull($this->_model->getId(), 'The admin user has an unexpected ID');
        $this->_model->loadByUsername(Mage_Admin_Utility_User::CRED_USERNAME);
        $this->assertNotEmpty($this->_model->getId(), 'The admin user should have been loaded');
    }
}
