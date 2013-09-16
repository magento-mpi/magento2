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
 *
 */
class Magento_WebsiteRestriction_Model_Observer
{
    /**
     * Website restriction data
     *
     * @var Magento_WebsiteRestriction_Helper_Data
     */
    protected $_websiteRestrictionData = null;

    /**
     * Customer data
     *
     * @var Magento_Customer_Helper_Data
     */
    protected $_customerData = null;

    /**
     * Core event manager proxy
     *
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager = null;

    /**
     * @var Magento_Core_Model_Store
     */
    protected $_store;

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_config;

    /**
     * @var Magento_Core_Model_Store_Config
     */
    protected $_storeConfig;

    /**
     * @var Magento_Core_Model_UrlFactory
     */
    protected $_urlFactory;

    /**
     * @var Magento_Core_Model_Session
     */
    protected $_session;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Customer_Helper_Data $customerData
     * @param Magento_WebsiteRestriction_Helper_Data $websiteRestrictionData
     * @param Magento_Core_Model_Store $store
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_Store_Config $storeConfig
     * @param Magento_Core_Model_UrlFactory $urlFactory
     * @param Magento_Core_Model_Session $session
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Customer_Helper_Data $customerData,
        Magento_WebsiteRestriction_Helper_Data $websiteRestrictionData,
        Magento_Core_Model_Store $store,
        Magento_Core_Model_Config $config,
        Magento_Core_Model_Store_Config $storeConfig,
        Magento_Core_Model_UrlFactory $urlFactory,
        Magento_Core_Model_Session $session
    ) {
        $this->_eventManager = $eventManager;
        $this->_customerData = $customerData;
        $this->_websiteRestrictionData = $websiteRestrictionData;
        $this->_store = $store;
        $this->_config = $config;
        $this->_storeConfig = $storeConfig;
        $this->_urlFactory = $urlFactory;
        $this->_session = $session;
    }

    /**
     * Implement website stub or private sales restriction
     *
     * @param Magento_Event_Observer $observer
     */
    public function restrictWebsite($observer)
    {
        /* @var $controller Magento_Core_Controller_Front_Action */
        $controller = $observer->getEvent()->getControllerAction();

        if (!$this->_store->isAdmin()) {
            $dispatchResult = new Magento_Object(array('should_proceed' => true, 'customer_logged_in' => false));
            $this->_eventManager->dispatch('websiterestriction_frontend', array(
                'controller' => $controller, 'result' => $dispatchResult
            ));
            if (!$dispatchResult->getShouldProceed()) {
                return;
            }
            if (!$this->_websiteRestrictionData->getIsRestrictionEnabled()) {
                return;
            }
            /* @var $request Magento_Core_Controller_Request_Http */
            $request    = $controller->getRequest();
            /* @var $response Magento_Core_Controller_Response_Http */
            $response   = $controller->getResponse();
            switch ((int)$this->_store->getConfig(Magento_WebsiteRestriction_Helper_Data::XML_PATH_RESTRICTION_MODE)) {
                // show only landing page with 503 or 200 code
                case Magento_WebsiteRestriction_Model_Mode::ALLOW_NONE:
                    if ($controller->getFullActionName() !== 'restriction_index_stub') {
                        $request->setModuleName('restriction')
                            ->setControllerName('index')
                            ->setActionName('stub')
                            ->setDispatched(false);
                        return;
                    }
                    $httpStatus = (int)$this->_store->getConfig(
                        Magento_WebsiteRestriction_Helper_Data::XML_PATH_RESTRICTION_HTTP_STATUS
                    );
                    if (Magento_WebsiteRestriction_Model_Mode::HTTP_503 === $httpStatus) {
                        $response->setHeader('HTTP/1.1','503 Service Unavailable');
                    }
                    break;

                case Magento_WebsiteRestriction_Model_Mode::ALLOW_REGISTER:
                    // break intentionally omitted

                // redirect to landing page/login
                case Magento_WebsiteRestriction_Model_Mode::ALLOW_LOGIN:
                    if (!$dispatchResult->getCustomerLoggedIn() && !$this->_customerData->isLoggedIn()) {
                        // see whether redirect is required and where
                        $redirectUrl = false;
                        $allowedActionNames = array_keys(
                            $this->_config->getNode(
                                Magento_WebsiteRestriction_Helper_Data::XML_NODE_RESTRICTION_ALLOWED_GENERIC
                            )->asArray()
                        );
                        if ($this->_customerData->isRegistrationAllowed()) {
                            $items = array_keys(
                                $this->_config->getNode(
                                    Magento_WebsiteRestriction_Helper_Data::XML_NODE_RESTRICTION_ALLOWED_REGISTER
                                )->asArray()
                            );
                            foreach($items as $fullActionName) {
                                $allowedActionNames[] = $fullActionName;
                            }
                        }

                        // to specified landing page
                        $restrictionRedirectCode = (int)$this->_store->getConfig(
                            Magento_WebsiteRestriction_Helper_Data::XML_PATH_RESTRICTION_HTTP_REDIRECT
                        );
                        if (Magento_WebsiteRestriction_Model_Mode::HTTP_302_LANDING === $restrictionRedirectCode) {
                            $cmsPageViewAction = 'cms_page_view';
                            $allowedActionNames[] = $cmsPageViewAction;
                            $pageIdentifier = $this->_store->getConfig(
                                Magento_WebsiteRestriction_Helper_Data::XML_PATH_RESTRICTION_LANDING_PAGE
                            );
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
                            $controller->setFlag('', Magento_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                        }
                        if ($this->_storeConfig->getConfigFlag(
                            Magento_Customer_Helper_Data::XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD
                        )) {
                            $afterLoginUrl = $this->_customerData->getDashboardUrl();
                        } else {
                            $afterLoginUrl = $this->getUrl();
                        }
                        $this->_session->setWebsiteRestrictionAfterLoginUrl($afterLoginUrl);
                    } elseif ($this->_session->hasWebsiteRestrictionAfterLoginUrl()) {
                        $response->setRedirect(
                            $this->_session->getWebsiteRestrictionAfterLoginUrl(true)
                        );
                        $controller->setFlag('', Magento_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                    }
                    break;
            }
        }
    }

    /**
     * Attempt to disallow customers registration
     *
     * @param Magento_Event_Observer $observer
     */
    public function restrictCustomersRegistration($observer)
    {
        $result = $observer->getEvent()->getResult();
        if ((!$this->_store->isAdmin()) && $result->getIsAllowed()) {
            $restrictionMode = (int)$this->_store->getConfig(
                Magento_WebsiteRestriction_Helper_Data::XML_PATH_RESTRICTION_MODE
            );
            $result->setIsAllowed((!$this->_websiteRestrictionData->getIsRestrictionEnabled())
                || (Magento_WebsiteRestriction_Model_Mode::ALLOW_REGISTER === $restrictionMode)
            );
        }
    }

    /**
     * Make layout load additional handler when in private sales mode
     *
     * @param Magento_Event_Observer $observer
     */
    public function addPrivateSalesLayoutUpdate($observer)
    {
        if (in_array((int)$this->_store->getConfig(Magento_WebsiteRestriction_Helper_Data::XML_PATH_RESTRICTION_MODE),
            array(
                Magento_WebsiteRestriction_Model_Mode::ALLOW_REGISTER,
                Magento_WebsiteRestriction_Model_Mode::ALLOW_LOGIN
            ),
            true
        )) {
            $observer->getEvent()->getLayout()->getUpdate()->addHandle('restriction_privatesales_mode');
        }
    }

    /**
     * @param string $route
     * @param array $params
     * @return Magento_Core_Model_Url
     */
    public function getUrl($route = '', $params = array())
    {
       return $this->_urlFactory->create()->getUrl($route, $params);
    }
}
