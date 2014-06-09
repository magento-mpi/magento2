<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Controller;

use Magento\Authz\Model\UserIdentifier;
use Magento\Customer\Service\V1\CustomerAccountServiceInterface;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Webapi\Exception as HttpException;

/**
 * Login controller
 */
class Login extends \Magento\Framework\App\Action\Action
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
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Session\Generic $session
     * @param CustomerAccountServiceInterface $customerAccountService
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Session\Generic $session,
        CustomerAccountServiceInterface $customerAccountService
    ) {
        parent::__construct($context);
        $this->session = $session;
        $this->customerAccountService = $customerAccountService;
    }

    /**
     * Login registered users and initiate a session. Send back the session id.
     *
     * Expects a POST in the form of {"username":"adugar@ebay.com", "password":"Welcome@1"}
     */
    public function indexAction()
    {
        $loginData = json_decode($this->getRequest()->getRawBody(), true);
        //$loginData will mostly be null for
        if (!$loginData || $this->getRequest()->getMethod()!==\Magento\Webapi\Model\Rest\Config::HTTP_METHOD_POST) {
            $this->getResponse()->setHttpResponseCode(HttpException::HTTP_BAD_REQUEST);
            return;
        }
        $customerData = null;
        try {
            $customerData = $this->customerAccountService->authenticate($loginData['username'], $loginData['password']);
        } catch (AuthenticationException $e) {
            $this->getResponse()->setHttpResponseCode(HttpException::HTTP_UNAUTHORIZED);
            //TODO: Verify error message that needs to be sent
            return;
        }
        $this->session->start('webapi');
        $this->session->setUserId($customerData->getId());
        $this->session->setUserType(UserIdentifier::USER_TYPE_CUSTOMER);
        $this->getResponse()->setBody($this->session->regenerateId(true)->getSessionId());
    }

    /**
     * Initiate a session for unregistered users. Send back the session id.
     */
    public function anonymousAction()
    {
        $this->session->start('webapi');
        $this->session->setUserId(0);
        $this->session->setUserType(UserIdentifier::USER_TYPE_GUEST);
        $this->getResponse()->setBody($this->session->regenerateId(true)->getSessionId());
    }
}
