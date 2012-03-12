<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * A parent class for adminhtml controllers - contains directives for admin user creation and logging in
 */
class Mage_Adminhtml_Utility_Controller extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @var Mage_Admin_Model_Session
     */
    protected $_session;

    /**
     * @var Mage_Admin_Utility_User
     */
    static protected $_utilityUser;

    protected function setUp()
    {
        parent::setUp();
        Mage::getSingleton('Mage_Adminhtml_Model_Url')->turnOffSecretKey();

        Mage_Admin_Utility_User::getInstance()
            ->createAdmin();

        $this->_session = new Mage_Admin_Model_Session();
        $this->_session->login('user', 'password');
    }

    protected function tearDown()
    {
        Mage_Admin_Utility_User::getInstance()
            ->destroyAdmin();

        Mage::getSingleton('Mage_Adminhtml_Model_Url')->turnOnSecretKey();
        parent::tearDown();
    }
}
