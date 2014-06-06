<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Service\V1;

use Magento\Authz\Model\UserIdentifier;
use Magento\Customer\Service\V1\CustomerAccountServiceInterface;

class LoginService implements LoginServiceInterface
{
    /**
     * @var \Magento\Framework\Session\Generic
     */
    protected $session;

    /**
     * @var CustomerAccountServiceInterface
     */
    protected $customerAccountService;

    /**
     * Initialize Login Service
     *
     * @param \Magento\Framework\Session\Generic $session
     * @param CustomerAccountServiceInterface $customerAccountService
     */
    public function __construct(
        \Magento\Framework\Session\Generic $session,
        CustomerAccountServiceInterface $customerAccountService
    ) {
        $this->session = $session;
        $this->customerAccountService = $customerAccountService;
    }

    /**
     * {@inheritdoc}
     */
    public function login($username, $password)
    {
        $customerData = $this->customerAccountService->authenticate($username, $password);
        $this->session->start('frontend');
        $this->session->setUserId($customerData->getId());
        $this->session->setUserType(UserIdentifier::USER_TYPE_CUSTOMER);
        return $this->session->regenerateId(true)->getSessionId();
    }

    /**
     * {@inheritdoc}
     */
    public function loginAnonymous()
    {
        $this->session->start('frontend');
        $this->session->setUserId(null);
        $this->session->setUserType(UserIdentifier::USER_TYPE_GUEST);
        return $this->session->regenerateId(true)->getSessionId();
    }
}