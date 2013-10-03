<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_WebsiteRestriction
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Private sales and stubs observer 
 */
namespace Magento\WebsiteRestriction\Model;

class Observer
{
    /**
     * @var \Magento\WebsiteRestriction\Model\ConfigInterface
     */
    protected $_config;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
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
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_storeConfig;

    /**
     * Core event manager proxy
     *
     * @var \Magento\Core\Model\Event\Manager
     */
    protected $_eventManager;

    /**
     * @var \Magento\Core\Model\UrlFactory
     */
    protected $_urlFactory;

    /**
     * @param \Magento\WebsiteRestriction\Model\ConfigInterface $config
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Customer\Helper\Data $customerHelper
     * @param \Magento\Core\Model\Session $session
     * @param \Magento\Core\Model\Store\Config $storeConfig
     * @param \Magento\Core\Model\UrlFactory $urlFactory
     */
    public function __construct(
        \Magento\WebsiteRestriction\Model\ConfigInterface $config,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Customer\Helper\Data $customerHelper,
        \Magento\Core\Model\Session $session,
        \Magento\Core\Model\Store\Config $storeConfig,
        \Magento\Core\Model\UrlFactory $urlFactory
    ) {
        $this->_config = $config;
        $this->_storeManager = $storeManager;
        $this->_eventManager = $eventManager;
        $this->_customerHelper = $customerHelper;
        $this->_session = $session;
        $this->_storeConfig = $storeConfig;
        $this->_urlFactory = $urlFactory;
    }

    /**
     * Implement website stub or private sales restriction
     *
     * @param \Magento\Event\Observer $observer
     */
    public function restrictWebsite($observer)
    {
        /* @var $controller \Magento\Core\Controller\Front\Action */
        $controller = $observer->getEvent()->getControllerAction();

        if (!$this->_storeManager->getStore()->isAdmin()) {
            $dispatchResult = new \Magento\Object(array('should_proceed' => true, 'customer_logged_in' => false));
            $this->_eventManager->dispatch('websiterestriction_frontend', array(
                'controller' => $controller, 'result' => $dispatchResult
            ));
            if (!$dispatchResult->getShouldProceed()) {
                return;
            }
            if (!$this->_config->isRestrictionEnabled()) {
                return;
            }
            /* @var $request \Magento\Core\Controller\Request\Http */
            $request    = $controller->getRequest();
            /* @var $response \Magento\Core\Controller\Response\Http */
            $response   = $controller->getResponse();
            switch ($this->_config->getMode()) {
                // show only landing page with 503 or 200 code
                case \Magento\WebsiteRestriction\Model\Mode::ALLOW_NONE:
                    if ($controller->getFullActionName() !== 'restriction_index_stub') {
                        $request->setModuleName('restriction')
                            ->setControllerName('index')
                            ->setActionName('stub')
                            ->setDispatched(false);
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
                            $allowedActionNames = array_merge(
                                $allowedActionNames,
                                $this->_config->getRegisterActions()
                            );
                        }

                        // to specified landing page
                        $restrictionRedirectCode = $this->_config->getHTTPRedirectCode();
                        if (\Magento\WebsiteRestriction\Model\Mode::HTTP_302_LANDING === $restrictionRedirectCode) {
                            $cmsPageViewAction = 'cms_page_view';
                            $allowedActionNames[] = $cmsPageViewAction;
                            $pageIdentifier = $this->_config->getLandingPageCode();
                            // Restrict access to CMS pages too
                            if (!in_array($controller->getFullActionName(), $allowedActionNames)
                                || ($controller->getFullActionName() === $cmsPageViewAction
                                    && $request->getAlias('rewrite_request_path') !== $pageIdentifier)
                            ) {
                                $redirectUrl = $this->getUrl('', array('_direct' => $pageIdentifier));
                            }
                        } elseif (!in_array($controller->getFullActionName(), $allowedActionNames)) {
                            // to login form
                            $redirectUrl = $this->getUrl('customer/account/login');
                        }

                        if ($redirectUrl) {
                            $response->setRedirect($redirectUrl);
                            $controller->setFlag('', \Magento\Core\Controller\Varien\Action::FLAG_NO_DISPATCH, true);
                        }
                        if ($this->_storeConfig->getConfigFlag(
                            \Magento\Customer\Helper\Data::XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD
                        )) {
                            $afterLoginUrl = $this->_customerHelper->getDashboardUrl();
                        } else {
                            $afterLoginUrl = $this->getUrl();
                        }
                        $this->_session->setWebsiteRestrictionAfterLoginUrl($afterLoginUrl);
                    } elseif ($this->_session->hasWebsiteRestrictionAfterLoginUrl()) {
                        $response->setRedirect(
                            $this->_session->getWebsiteRestrictionAfterLoginUrl(true)
                        );
                        $controller->setFlag('', \Magento\Core\Controller\Varien\Action::FLAG_NO_DISPATCH, true);
                    }
                    break;
            }
        }
    }

    /**
     * Make layout load additional handler when in private sales mode
     *
     * @param \Magento\Event\Observer $observer
     */
    public function addPrivateSalesLayoutUpdate($observer)
    {
        if (in_array($this->_config->getMode(),
            array(
                \Magento\WebsiteRestriction\Model\Mode::ALLOW_REGISTER,
                \Magento\WebsiteRestriction\Model\Mode::ALLOW_LOGIN
            ),
            true
        )) {
            $observer->getEvent()->getLayout()->getUpdate()->addHandle('restriction_privatesales_mode');
        }
    }

    /**
     * @param string $route
     * @param array $params
     * @return \Magento\Core\Model\Url
     */
    public function getUrl($route = '', $params = array())
    {
       return $this->_urlFactory->create()->getUrl($route, $params);
    }
}
