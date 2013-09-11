<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webapi
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoDataFixture Magento/Webapi/_files/user_with_role.php
 */
class Magento_Webapi_Model_Soap_Security_UsernameTokenTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_TestFramework_ObjectManager
     */
    protected $_objectManager;

    /** @var \Magento\Webapi\Model\Acl\User */
    protected $_user;

    /**
     * Set up object manager and user factory.
     */
    protected function setUp()
    {
        $this->_objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $this->_objectManager->addSharedInstance(
            Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento\Core\Model\Dir'),
            '\Magento\Core\Model\Dir'
        );
        $userFactory = new \Magento\Webapi\Model\Acl\User\Factory($this->_objectManager);
        $this->_user = $userFactory->create();
        $this->_user->load('test_username', 'api_key');
    }

    /**
     * Test positive authentication with text password type.
     */
    public function testAuthenticatePasswordText()
    {
        /** @var \Magento\Webapi\Model\Soap\Security\UsernameToken $usernameToken */
        $usernameToken = $this->_objectManager->create('Magento\Webapi\Model\Soap\Security\UsernameToken', array(
            'passwordType' => \Magento\Webapi\Model\Soap\Security\UsernameToken::PASSWORD_TYPE_TEXT
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
        /** @var \Magento\Webapi\Model\Soap\Security\UsernameToken $usernameToken */
        $usernameToken = $this->_objectManager->create('Magento\Webapi\Model\Soap\Security\UsernameToken');

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
     * @expectedException \Magento\Webapi\Model\Soap\Security\UsernameToken\NonceUsedException
     */
    public function testAuthenticateWithNonceUsed()
    {
        /** @var \Magento\Webapi\Model\Soap\Security\UsernameToken $usernameToken */
        $usernameToken = $this->_objectManager->create('Magento\Webapi\Model\Soap\Security\UsernameToken');

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
