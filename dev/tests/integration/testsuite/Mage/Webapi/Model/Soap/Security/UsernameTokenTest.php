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

    /** @var Mage_Webapi_Model_Acl_User */
    protected $_user;

    /**
     * Set up object manager and user factory.
     */
    protected function setUp()
    {
        $this->_objectManager = Mage::getObjectManager();
        $this->_objectManager->addSharedInstance(
            Mage::getObjectManager()->get('Magento_Core_Model_Dir'),
            'Magento_Core_Model_Dir'
        );
        $userFactory = new Mage_Webapi_Model_Acl_User_Factory($this->_objectManager);
        $this->_user = $userFactory->create();
        $this->_user->load('test_username', 'api_key');
    }

    /**
     * Test positive authentication with text password type.
     */
    public function testAuthenticatePasswordText()
    {
        /** @var Mage_Webapi_Model_Soap_Security_UsernameToken $usernameToken */
        $usernameToken = $this->_objectManager->create('Mage_Webapi_Model_Soap_Security_UsernameToken', array(
            'passwordType' => Mage_Webapi_Model_Soap_Security_UsernameToken::PASSWORD_TYPE_TEXT
        ));

        $created = date('c');
        $nonce = base64_encode(mt_rand());
        $authenticatedUser = $usernameToken->authenticate($this->_user->getApiKey(), $this->_user->getSecret(),
            $created, $nonce);
        $this->assertEquals($this->_user->getRoleId(), $authenticatedUser->getRoleId());
    }

    /**
     * Test positive authentication with digest password type.
     */
    public function testAuthenticatePasswordDigest()
    {
        /** @var Mage_Webapi_Model_Soap_Security_UsernameToken $usernameToken */
        $usernameToken = $this->_objectManager->create('Mage_Webapi_Model_Soap_Security_UsernameToken');

        $created = date('c');
        $nonce = mt_rand();
        $password = base64_encode(hash('sha1', $nonce . $created . $this->_user->getSecret(), true));
        $nonce = base64_encode($nonce);
        $authenticatedUser = $usernameToken->authenticate($this->_user->getApiKey(), $password, $created, $nonce);
        $this->assertEquals($this->_user->getRoleId(), $authenticatedUser->getRoleId());
    }

    /**
     * Test negative authentication with used nonce-timestamp pair.
     *
     * @expectedException Mage_Webapi_Model_Soap_Security_UsernameToken_NonceUsedException
     */
    public function testAuthenticateWithNonceUsed()
    {
        /** @var Mage_Webapi_Model_Soap_Security_UsernameToken $usernameToken */
        $usernameToken = $this->_objectManager->create('Mage_Webapi_Model_Soap_Security_UsernameToken');

        $created = date('c');
        $nonce = mt_rand();
        $password = base64_encode(hash('sha1', $nonce . $created . $this->_user->getSecret(), true));
        $nonce = base64_encode($nonce);
        $authenticatedUser = $usernameToken->authenticate($this->_user->getApiKey(), $password, $created, $nonce);
        $this->assertEquals($this->_user, $authenticatedUser);
        // Try to authenticate with the same nonce and timestamp
        $usernameToken->authenticate($this->_user->getApiKey(), $password, $created, $nonce);
    }
}
