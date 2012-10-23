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
    public function testAuthenticatePasswordText()
    {
        $userFixture = new Mage_Webapi_Model_Acl_User();
        $userFixture->load('test_username', 'api_key');

        $usernameToken = new Mage_Webapi_Model_Soap_Security_UsernameToken(array(
            'username' => $userFixture->getApiKey(),
            'passwordType' => Mage_Webapi_Model_Soap_Security_UsernameToken::PASSWORD_TYPE_TEXT,
            'password' => $userFixture->getApiSecret(),
            'nonce' => base64_encode(mt_rand()),
            'created' => date('c')
        ));

        $authenticatedUser = $usernameToken->authenticate();
        $this->assertEquals($userFixture->getRoleId(), $authenticatedUser->getRoleId());
    }

    public function testAuthenticatePasswordDigest()
    {
        $userFixture = new Mage_Webapi_Model_Acl_User();
        $userFixture->load('test_username', 'api_key');

        $nonce = mt_rand();
        $timestamp = date('c');
        $usernameToken = new Mage_Webapi_Model_Soap_Security_UsernameToken(array(
            'username' => $userFixture->getApiKey(),
            'passwordType' => Mage_Webapi_Model_Soap_Security_UsernameToken::PASSWORD_TYPE_DIGEST,
            'password' => base64_encode(hash('sha1', $nonce . $timestamp . $userFixture->getApiSecret(), true)),
            'nonce' => base64_encode($nonce),
            'created' => $timestamp
        ));

        $authenticatedUser = $usernameToken->authenticate();
        $this->assertEquals($userFixture->getRoleId(), $authenticatedUser->getRoleId());
    }

    /**
     * @expectedException Mage_Webapi_Model_Soap_Security_UsernameToken_NonceUsedException
     */
    public function testAuthenticateWithNonceUsed()
    {
        $userFixture = new Mage_Webapi_Model_Acl_User();
        $userFixture->load('test_username', 'api_key');

        $nonce = mt_rand();
        $timestamp = date('c');
        $options = array(
            'username' => $userFixture->getApiKey(),
            'passwordType' => Mage_Webapi_Model_Soap_Security_UsernameToken::PASSWORD_TYPE_DIGEST,
            'password' => base64_encode(hash('sha1', $nonce . $timestamp . $userFixture->getApiSecret(), true)),
            'nonce' => base64_encode($nonce),
            'created' => $timestamp
        );
        $usernameToken = new Mage_Webapi_Model_Soap_Security_UsernameToken($options);
        $this->assertInstanceOf('Mage_Webapi_Model_Soap_Security_UsernameToken', $usernameToken);
        // Try to create username token with the same nonce/timestamp
        new Mage_Webapi_Model_Soap_Security_UsernameToken($options);
    }
}
