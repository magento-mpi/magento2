<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Backend_Controller_ActionAbstract.
 *
 */
class Mage_Backend_Controller_ActionAbstractTest extends Mage_Adminhtml_Utility_Controller
{
    /**
     * Check redirection to startup page for logged user
     * @magentoConfigFixture admin/routers/adminhtml/args/frontName admin
     * @magentoConfigFixture current_store admin/security/use_form_key 1
     */
    public function testPreDispatchWithEmptyUrlRedirectsToStartupPage()
    {
        $expected = Mage::getSingleton('Mage_Backend_Model_Url')->getUrl('adminhtml/dashboard');
        $this->dispatch('/admin');
        $this->assertRedirect($expected, self::MODE_START_WITH);
    }

    /**
     * Check login redirection
     *
     * @covers Mage_Backend_Controller_ActionAbstract::_initAuthentication
     * @magentoDataFixture emptyDataFixture
     */
    public function testInitAuthentication()
    {
        /**
         * Logout current session
         */
        $this->_auth->logout();

        $postLogin = array('login' => array(
            'username' => Magento_Test_Bootstrap::ADMIN_NAME,
            'password' => Magento_Test_Bootstrap::ADMIN_PASSWORD
        ));

        $this->getRequest()->setPost($postLogin);
        $url = Mage::getSingleton('Mage_Backend_Model_Url')->getUrl('adminhtml/system_account/index');
        $this->dispatch($url);

        $expected = 'admin/system_account/index';
        $this->assertRedirect($expected, self::MODE_CONTAINS);
    }

    /**
     * Empty data fixture to provide support of transaction
     * @static
     *
     */
    public static function emptyDataFixture()
    {

    }
}
