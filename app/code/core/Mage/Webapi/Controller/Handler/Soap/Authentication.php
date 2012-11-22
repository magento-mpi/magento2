<?php
/**
 * SOAP web API authentication model.
 *
 * @copyright {}
 */
class Mage_Webapi_Controller_Handler_Soap_Authentication
{
    /** @var Mage_Webapi_Helper_Data */
    protected $_helper;

    /**
     * Username token factory.
     *
     * @var Mage_Webapi_Model_Soap_Security_UsernameToken_Factory
     */
    protected $_tokenFactory;

    /** @var Mage_Webapi_Model_Authorization_RoleLocator */
    protected $_roleLocator;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     * @param Mage_Webapi_Model_Soap_Security_UsernameToken_Factory $usernameTokenFactory
     * @param Mage_Webapi_Model_Authorization_RoleLocator $roleLocator
     */
    public function __construct(
        Mage_Core_Model_Factory_Helper $helperFactory,
        Mage_Webapi_Model_Soap_Security_UsernameToken_Factory $usernameTokenFactory,
        Mage_Webapi_Model_Authorization_RoleLocator $roleLocator
    ) {
        $this->_helper = $helperFactory->get('Mage_Webapi_Helper_Data');
        $this->_tokenFactory = $usernameTokenFactory;
        $this->_roleLocator = $roleLocator;
    }

    /**
     * Authenticate user.
     *
     * @param stdClass $usernameToken WS-Security UsernameToken object
     * @throws Mage_Webapi_Exception If authentication failed
     */
    public function authenticate($usernameToken)
    {
        try {
            $token = $this->_tokenFactory->createFromArray();
            $request = $usernameToken;
            // @codingStandardsIgnoreStart
            $user = $token->authenticate($request->Username, $request->Password, $request->Created, $request->Nonce);
            // @codingStandardsIgnoreEnd
            $this->_roleLocator->setRoleId($user->getRoleId());
        } catch (Mage_Webapi_Model_Soap_Security_UsernameToken_NonceUsedException $e) {
            throw new Mage_Webapi_Exception(
                $this->_helper->__('WS-Security UsernameToken Nonce is already used.'),
                Mage_Webapi_Exception::HTTP_BAD_REQUEST
            );
        } catch (Mage_Webapi_Model_Soap_Security_UsernameToken_TimestampRefusedException $e) {
            throw new Mage_Webapi_Exception(
                $this->_helper->__('WS-Security UsernameToken Created timestamp is refused.'),
                Mage_Webapi_Exception::HTTP_BAD_REQUEST
            );
        } catch (Mage_Webapi_Model_Soap_Security_UsernameToken_InvalidCredentialException $e) {
            throw new Mage_Webapi_Exception(
                $this->_helper->__('Invalid Username or Password.'),
                Mage_Webapi_Exception::HTTP_BAD_REQUEST
            );
        }
    }
}
