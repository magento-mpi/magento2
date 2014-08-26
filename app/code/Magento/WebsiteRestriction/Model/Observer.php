<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\WebsiteRestriction\Model;

/**
 * Private sales and stubs observer
 */
class Observer
{
    /**
     * @var \Magento\WebsiteRestriction\Model\ConfigInterface
     */
    protected $_config;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Customer\Helper\Data
     */
    protected $_customerHelper;

    /**
     * @var \Magento\Framework\Session\Generic
     */
    protected $_session;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Core event manager proxy
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var \Magento\Framework\UrlFactory
     */
    protected $_urlFactory;

    /**
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $_actionFlag;

    /**
     * @param \Magento\WebsiteRestriction\Model\ConfigInterface $config
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Customer\Helper\Data $customerHelper
     * @param \Magento\Framework\Session\Generic $session
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\UrlFactory $urlFactory
     * @param \Magento\Framework\App\ActionFlag $actionFlag
     */
    public function __construct(
        \Magento\WebsiteRestriction\Model\ConfigInterface $config,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Customer\Helper\Data $customerHelper,
        \Magento\Framework\Session\Generic $session,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\UrlFactory $urlFactory,
        \Magento\Framework\App\ActionFlag $actionFlag
    ) {
        $this->_config = $config;
        $this->_storeManager = $storeManager;
        $this->_eventManager = $eventManager;
        $this->_customerHelper = $customerHelper;
        $this->_session = $session;
        $this->_scopeConfig = $scopeConfig;
        $this->_urlFactory = $urlFactory;
        $this->_actionFlag = $actionFlag;
    }

    /**
     * Implement website stub or private sales restriction
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function restrictWebsite($observer)
    {
        /* @var $controller \Magento\Framework\App\Action\Action */
        $controller = $observer->getEvent()->getControllerAction();

        $dispatchResult = new \Magento\Framework\Object(array('should_proceed' => true, 'customer_logged_in' => false));
        $this->_eventManager->dispatch(
            'websiterestriction_frontend',
            array('controller' => $controller, 'result' => $dispatchResult)
        );
        if (!$dispatchResult->getShouldProceed()) {
            return;
        }
        if (!$this->_config->isRestrictionEnabled()) {
            return;
        }
        /* @var $request \Magento\Framework\App\RequestInterface */
        $request = $observer->getEvent()->getRequest();
        /* @var $response \Magento\Framework\App\ResponseInterface */
        $response = $controller->getResponse();
        switch ($this->_config->getMode()) {
            // show only landing page with 503 or 200 code
            case \Magento\WebsiteRestriction\Model\Mode::ALLOW_NONE:
                if ($request->getFullActionName() !== 'restriction_index_stub') {
                    $request->setModuleName(
                        'restriction'
                    )->setControllerName(
                        'index'
                    )->setActionName(
                        'stub'
                    )->setDispatched(
                        false
                    );
                    return;
                }
                $httpStatus = $this->_config->getHTTPStatusCode();
                if (\Magento\WebsiteRestriction\Model\Mode::HTTP_503 === $httpStatus) {
                    $response->setHeader('HTTP/1.1', '503 Service Unavailable');
                }
                break;

            case \Magento\WebsiteRestriction\Model\Mode::ALLOW_REGISTER:
                // break intentionally omitted

                //redirect to landing page/login
            case \Magento\WebsiteRestriction\Model\Mode::ALLOW_LOGIN:
                if (!$dispatchResult->getCustomerLoggedIn() && !$this->_customerHelper->isLoggedIn()) {
                    // see whether redirect is required and where
                    $redirectUrl = false;
                    $allowedActionNames = $this->_config->getGenericActions();
                    if ($this->_customerHelper->isRegistrationAllowed()) {
                        $allowedActionNames = array_merge($allowedActionNames, $this->_config->getRegisterActions());
                    }

                    // to specified landing page
                    $restrictionRedirectCode = $this->_config->getHTTPRedirectCode();
                    if (\Magento\WebsiteRestriction\Model\Mode::HTTP_302_LANDING === $restrictionRedirectCode) {
                        $cmsPageViewAction = 'cms_page_view';
                        $allowedActionNames[] = $cmsPageViewAction;
                        $pageIdentifier = $this->_config->getLandingPageCode();
                        // Restrict access to CMS pages too
                        if (!in_array(
                            $request->getFullActionName(),
                            $allowedActionNames
                        ) || $request->getFullActionName() === $cmsPageViewAction && $request->getAlias(
                            'rewrite_request_path'
                        ) !== $pageIdentifier
                        ) {
                            $redirectUrl = $this->getUrl('', array('_direct' => $pageIdentifier));
                        }
                    } elseif (!in_array($request->getFullActionName(), $allowedActionNames)) {
                        // to login form
                        $redirectUrl = $this->getUrl('customer/account/login');
                    }

                    if ($redirectUrl) {
                        $response->setRedirect($redirectUrl);
                        $this->_actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
                    }
                    if ($this->_scopeConfig->isSetFlag(
                        \Magento\Customer\Helper\Data::XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    )
                    ) {
                        $afterLoginUrl = $this->_customerHelper->getDashboardUrl();
                    } else {
                        $afterLoginUrl = $this->getUrl();
                    }
                    $this->_session->setWebsiteRestrictionAfterLoginUrl($afterLoginUrl);
                } elseif ($this->_session->hasWebsiteRestrictionAfterLoginUrl()) {
                    $response->setRedirect($this->_session->getWebsiteRestrictionAfterLoginUrl(true));
                    $this->_actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
                }
                break;
        }
    }

    /**
     * Make layout load additional handler when in private sales mode
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function addPrivateSalesLayoutUpdate($observer)
    {
        if (in_array(
            $this->_config->getMode(),
            array(
                \Magento\WebsiteRestriction\Model\Mode::ALLOW_REGISTER,
                \Magento\WebsiteRestriction\Model\Mode::ALLOW_LOGIN
            ),
            true
        )
        ) {
            $observer->getEvent()->getLayout()->getUpdate()->addHandle('restriction_privatesales_mode');
        }
    }

    /**
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl($route = '', $params = array())
    {
        return $this->_urlFactory->create()->getUrl($route, $params);
    }
}
