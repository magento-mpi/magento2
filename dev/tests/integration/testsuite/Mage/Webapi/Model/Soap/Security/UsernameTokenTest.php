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
 * @magentoDataFixture Mage/Webapi/_files/user_with_role.php
 */
class Mage_Webapi_Model_Soap_Security_UsernameTokenTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Test_ObjectManager
     */
    protected $_objectManager;

    /** @var Mage_Webapi_Model_Acl_User_Factory */
    protected $_userFactory;

    /**
     * Set up object manager and user factory.
     */
    protected function setUp()
    {
        $this->_objectManager = new Magento_Test_ObjectManager();
        $this->_userFactory = new Mage_Webapi_Model_Acl_User_Factory($this->_objectManager);
    }

    /**
     * Clean up.
     */
    protected function tearDown()
    {
        unset($this->_objectManager);
        unset($this->_userFactory);
    }

    /**
     * Test positive authenticate with text password type.
     */
    public function testAuthenticatePasswordText()
    {
        $user = $this->_userFactory->create();
        $user->load('test_username', 'api_key');
        /** @var Mage_Webapi_Model_Soap_Security_UsernameToken $usernameToken */
        $usernameToken = $this->_objectManager->create('Mage_Webapi_Model_Soap_Security_UsernameToken', array(
            'passwordType' => Mage_Webapi_Model_Soap_Security_UsernameToken::PASSWORD_TYPE_TEXT
        ));

        $created = date('c');
        $nonce = base64_encode(mt_rand());
        $authenticatedUser = $usernameToken->authenticate($user->getApiKey(), $user->getSecret(), $created, $nonce);
        $this->assertEquals($user->getRoleId(), $authenticatedUser->getRoleId());
    }

    /**
     * Test positive authenticate with digest password type
     */
    public function testAuthenticatePasswordDigest()
    {
        $user = $this->_userFactory->create();
        $user->load('test_username', 'api_key');
        /** @var Mage_Webapi_Model_Soap_Security_UsernameToken $usernameToken */
        $usernameToken = $this->_objectManager->create('Mage_Webapi_Model_Soap_Security_UsernameToken');

        $created = date('c');
        $nonce = mt_rand();
        $password = base64_encode(hash('sha1', $nonce . $created . $user->getSecret(), true));
        $nonce = base64_encode($nonce);
        $authenticatedUser = $usernameToken->authenticate($user->getApiKey(), $password, $created, $nonce);
        $this->assertEquals($user->getRoleId(), $authenticatedUser->getRoleId());
    }

    /**
     * Test negative authentication with used nonce-timestamp pair.
     *
     * @expectedException Mage_Webapi_Model_Soap_Security_UsernameToken_NonceUsedException
     */
    public function testAuthenticateWithNonceUsed()
    {
        $user = $this->_userFactory->create();
        $user->load('test_username', 'api_key');
        /** @var Mage_Webapi_Model_Soap_Security_UsernameToken $usernameToken */
        $usernameToken = $this->_objectManager->create('Mage_Webapi_Model_Soap_Security_UsernameToken');

        $created = date('c');
        $nonce = mt_rand();
        $password = base64_encode(hash('sha1', $nonce . $created . $user->getSecret(), true));
        $nonce = base64_encode($nonce);
        $authenticatedUser = $usernameToken->authenticate($user->getApiKey(), $password, $created, $nonce);
        $this->assertEquals($user, $authenticatedUser);
        // Try to authenticate with the same nonce and timestamp
        $usernameToken->authenticate($user->getApiKey(), $password, $created, $nonce);
    }
}
