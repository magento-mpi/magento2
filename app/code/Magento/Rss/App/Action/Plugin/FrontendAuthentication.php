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
class FrontendAuthentication extends \Magento\Catalog\Model\App\Action\ContextPlugin
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
     * @var \Magento\Catalog\Model\Product\ProductList\Toolbar
     */
    protected $toolbarModel;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \Magento\Catalog\Helper\Product\ProductList
     */
    protected $productListHelper;

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
     * @param \Magento\Catalog\Model\Product\ProductList\Toolbar $toolbarModel
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Catalog\Helper\Product\ProductList $productListHelper
     * @param \Magento\Framework\Logger $logger
     * @param \Magento\Framework\App\ResponseInterface $response
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccountService,
        \Magento\Framework\HTTP\Authentication $httpAuthentication,
        \Magento\Framework\Logger $logger,
        \Magento\Framework\App\ResponseInterface $response,
        \Magento\Catalog\Model\Product\ProductList\Toolbar $toolbarModel,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Catalog\Helper\Product\ProductList $productListHelper
    ) {
        $this->customerSession = $customerSession;
        $this->customerAccountService = $customerAccountService;
        $this->httpAuthentication = $httpAuthentication;
        $this->logger = $logger;
        $this->response = $response;
        parent::__construct($toolbarModel, $httpContext, $productListHelper);
    }

    /**
     * Replace standard admin login form with HTTP Basic authentication
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundDispatch(
        \Magento\Framework\App\Action\Action $subject,
        \Closure $proceed,
        \Magento\Framework\App\RequestInterface $request
    )
    {
        // Try to login using HTTP-authentication
        if (!$this->customerSession->isLoggedIn()) {
            list($login, $password) = $this->httpAuthentication->getCredentials();
            try {
                $customer = $this->customerAccountService->authenticate($login, $password);
                $this->customerSession->setCustomerDataAsLoggedIn($customer);
                $this->customerSession->regenerateId();
            } catch (\Magento\Framework\Exception\InvalidEmailOrPasswordException $e) {
                $this->logger->logException($e);
            }
        }

        // Verify if logged in and authorized
        if (!$this->customerSession->isLoggedIn()) {
            $this->httpAuthentication->setAuthenticationFailed('RSS Feeds');
            return $this->response;
        }
        return parent::aroundDispatch($subject, $proceed, $request);
    }
}
