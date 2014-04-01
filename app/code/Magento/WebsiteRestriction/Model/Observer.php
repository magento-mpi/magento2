<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_WebsiteRestriction
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
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Customer\Helper\Data
     */
    protected $_customerHelper;

    /**
     * @var \Magento\Core\Model\Session
     */
    protected $_session;

    /**
     * @var \Magento\App\Config\ScopeConfigInterface
     */
    protected $_storeConfig;

    /**
     * Core event manager proxy
     *
     * @var \Magento\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var \Magento\UrlFactory
     */
    protected $_urlFactory;

    /**
     * @var \Magento\App\ActionFlag
     */
    protected $_actionFlag;

    /**
     * @param \Magento\WebsiteRestriction\Model\ConfigInterface $config
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Customer\Helper\Data $customerHelper
     * @param \Magento\Core\Model\Session $session
     * @param \Magento\App\Config\ScopeConfigInterface $storeConfig
     * @param \Magento\UrlFactory $urlFactory
     * @param \Magento\App\ActionFlag $actionFlag
     */
    public function __construct(
        \Magento\WebsiteRestriction\Model\ConfigInterface $config,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Customer\Helper\Data $customerHelper,
        \Magento\Core\Model\Session $session,
        \Magento\App\Config\ScopeConfigInterface $storeConfig,
        \Magento\UrlFactory $urlFactory,
        \Magento\App\ActionFlag $actionFlag
    ) {
        $this->_config = $config;
        $this->_storeManager = $storeManager;
        $this->_eventManager = $eventManager;
        $this->_customerHelper = $customerHelper;
        $this->_session = $session;
        $this->_storeConfig = $storeConfig;
        $this->_urlFactory = $urlFactory;
        $this->_actionFlag = $actionFlag;
    }

    /**
     * Implement website stub or private sales restriction
     *
     * @param \Magento\Event\Observer $observer
     * @return void
     */
    public function restrictWebsite($observer)
    {
        /* @var $controller \Magento\App\Action\Action */
        $controller = $observer->getEvent()->getControllerAction();

        $dispatchResult = new \Magento\Object(array('should_proceed' => true, 'customer_logged_in' => false));
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
        /* @var $request \Magento\App\RequestInterface */
        $request = $observer->getEvent()->getRequest();
        /* @var $response \Magento\App\ResponseInterface */
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
                        $this->_actionFlag->set('', \Magento\App\Action\Action::FLAG_NO_DISPATCH, true);
                    }
                    if ($this->_storeConfig->isSetFlag(
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
                    $this->_actionFlag->set('', \Magento\App\Action\Action::FLAG_NO_DISPATCH, true);
                }
                break;
        }
    }

    /**
     * Make layout load additional handler when in private sales mode
     *
     * @param \Magento\Event\Observer $observer
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
