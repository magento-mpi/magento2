<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rss\App\Action\Plugin;

/**
 * Class FrontendAuthentication
 */
class FrontendAuthentication
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Customer\Service\V1\CustomerAccountServiceInterface
     */
    protected $customerAccountService;

    /**
     * @var \Magento\Framework\HTTP\Authentication
     */
    protected $httpAuthentication;

    /**
     * @var \Magento\Framework\Logger
     */
    protected $logger;

    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $response;

    /**
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccountService
     * @param \Magento\Framework\HTTP\Authentication $httpAuthentication
     * @param \Magento\Framework\Logger $logger
     * @param \Magento\Framework\App\ResponseInterface $response
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccountService,
        \Magento\Framework\HTTP\Authentication $httpAuthentication,
        \Magento\Framework\Logger $logger,
        \Magento\Framework\App\ResponseInterface $response
    ) {
        $this->customerSession = $customerSession;
        $this->customerAccountService = $customerAccountService;
        $this->httpAuthentication = $httpAuthentication;
        $this->logger = $logger;
        $this->response = $response;
    }

    /**
     * Replace standard admin login form with HTTP Basic authentication
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param \Magento\Framework\App\Action\Action $subject
     * @param callable $proceed
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function aroundDispatch(
        \Magento\Framework\App\Action\Action $subject,
        \Closure $proceed,
        \Magento\Framework\App\RequestInterface $request
    ) {
        // Try to login using HTTP-authentication
        if (!$this->customerSession->isLoggedIn()) {
            list($login, $password) = $this->httpAuthentication->getCredentials();
            try {
                $customer = $this->customerAccountService->authenticate($login, $password);
                $this->customerSession->setCustomerDataAsLoggedIn($customer);
                $this->customerSession->regenerateId();
            } catch (\Exception $e) {
                $this->logger->logException($e);
            }
        }

        // Verify if logged in and authorized
        if (!$this->customerSession->isLoggedIn()) {
            $this->httpAuthentication->setAuthenticationFailed('RSS Feeds');
            return $this->response;
        }
        return $proceed($request);
    }
}
