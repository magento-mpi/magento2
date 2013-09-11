<?php
/**
 * SOAP web API authentication model.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Controller\Dispatcher\Soap;

class Authentication
{
    /**
     * Username token factory.
     *
     * @var \Magento\Webapi\Model\Soap\Security\UsernameToken\Factory
     */
    protected $_tokenFactory;

    /** @var \Magento\Webapi\Model\Authorization\RoleLocator */
    protected $_roleLocator;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Webapi\Model\Soap\Security\UsernameToken\Factory $usernameTokenFactory
     * @param \Magento\Webapi\Model\Authorization\RoleLocator $roleLocator
     */
    public function __construct(
        \Magento\Webapi\Model\Soap\Security\UsernameToken\Factory $usernameTokenFactory,
        \Magento\Webapi\Model\Authorization\RoleLocator $roleLocator
    ) {
        $this->_tokenFactory = $usernameTokenFactory;
        $this->_roleLocator = $roleLocator;
    }

    /**
     * Authenticate user.
     *
     * @param \stdClass $usernameToken WS-Security UsernameToken object
     * @throws \Magento\Webapi\Exception If authentication failed
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
        } catch (\Magento\Webapi\Model\Soap\Security\UsernameToken\NonceUsedException $e) {
            throw new \Magento\Webapi\Exception(
                __('WS-Security UsernameToken Nonce is already used.'),
                \Magento\Webapi\Exception::HTTP_BAD_REQUEST
            );
        } catch (\Magento\Webapi\Model\Soap\Security\UsernameToken\TimestampRefusedException $e) {
            throw new \Magento\Webapi\Exception(
                __('WS-Security UsernameToken Created timestamp is refused.'),
                \Magento\Webapi\Exception::HTTP_BAD_REQUEST
            );
        } catch (\Magento\Webapi\Model\Soap\Security\UsernameToken\InvalidCredentialException $e) {
            throw new \Magento\Webapi\Exception(
                __('Invalid Username or Password.'),
                \Magento\Webapi\Exception::HTTP_BAD_REQUEST
            );
        } catch (\Magento\Webapi\Model\Soap\Security\UsernameToken\InvalidDateException $e) {
            throw new \Magento\Webapi\Exception(
                __('Invalid UsernameToken Created date.'),
                \Magento\Webapi\Exception::HTTP_BAD_REQUEST
            );
        }
    }
}
