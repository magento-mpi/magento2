<?php
/**
 * SOAP web API authentication model.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Soap_Authentication
{
    /**
     * Username token factory.
     *
     * @var Magento_Webapi_Model_Soap_Security_UsernameToken_Factory
     */
    protected $_tokenFactory;

    /** @var Magento_Webapi_Model_Authorization_RoleLocator */
    protected $_roleLocator;

    /**
     * Initialize dependencies.
     *
     * @param Magento_Webapi_Model_Soap_Security_UsernameToken_Factory $usernameTokenFactory
     * @param Magento_Webapi_Model_Authorization_RoleLocator $roleLocator
     */
    public function __construct(
        Magento_Webapi_Model_Soap_Security_UsernameToken_Factory $usernameTokenFactory,
        Magento_Webapi_Model_Authorization_RoleLocator $roleLocator
    ) {
        $this->_tokenFactory = $usernameTokenFactory;
        $this->_roleLocator = $roleLocator;
    }

    /**
     * Authenticate user.
     *
     * @param stdClass $usernameToken WS-Security UsernameToken object
     * @throws Magento_Webapi_Exception If authentication failed
     */
    public function authenticate($usernameToken)
    {
        try {
            $token = $this->_tokenFactory->create();
            $request = $usernameToken;
            // @codingStandardsIgnoreStart
            $user = $token->authenticate($request->Username, $request->Password, $request->Created, $request->Nonce);
            // @codingStandardsIgnoreEnd
            $this->_roleLocator->setRoleId($user->getRoleId());
        } catch (Magento_Webapi_Model_Soap_Security_UsernameToken_NonceUsedException $e) {
            throw new Magento_Webapi_Exception(
                __('WS-Security UsernameToken Nonce is already used.'),
                Magento_Webapi_Exception::HTTP_BAD_REQUEST
            );
        } catch (Magento_Webapi_Model_Soap_Security_UsernameToken_TimestampRefusedException $e) {
            throw new Magento_Webapi_Exception(
                __('WS-Security UsernameToken Created timestamp is refused.'),
                Magento_Webapi_Exception::HTTP_BAD_REQUEST
            );
        } catch (Magento_Webapi_Model_Soap_Security_UsernameToken_InvalidCredentialException $e) {
            throw new Magento_Webapi_Exception(
                __('Invalid Username or Password.'),
                Magento_Webapi_Exception::HTTP_BAD_REQUEST
            );
        } catch (Magento_Webapi_Model_Soap_Security_UsernameToken_InvalidDateException $e) {
            throw new Magento_Webapi_Exception(
                __('Invalid UsernameToken Created date.'),
                Magento_Webapi_Exception::HTTP_BAD_REQUEST
            );
        }
    }
}
