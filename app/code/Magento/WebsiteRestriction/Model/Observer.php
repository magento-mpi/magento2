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
     * @var Magento_WebsiteRestriction_Model_ConfigInterface
     */
    protected $_config;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_Customer_Helper_Data
     */
    protected $_customerHelper;

    /**
     * @var Magento_Core_Model_Session
     */
    protected $_session;

    /**
     * @var Magento_Core_Model_Store_Config
     */
    protected $_storeConfig;

    /**
     * Core event manager proxy
     *
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager = null;

    /**
     * @param Magento_WebsiteRestriction_Model_ConfigInterface $config
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Customer_Helper_Data $customerHelper
     * @param Magento_Core_Model_Session $session
     * @param Magento_Core_Model_Store_Config $storeConfig
     */
    public function __construct(
        Magento_WebsiteRestriction_Model_ConfigInterface $config,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Customer_Helper_Data $customerHelper,
        Magento_Core_Model_Session $session,
        Magento_Core_Model_Store_Config $storeConfig
    ) {
        $this->_config = $config;
        $this->_storeManager = $storeManager;
        $this->_eventManager = $eventManager;
        $this->_customerHelper = $customerHelper;
        $this->_session = $session;
        $this->_storeConfig = $storeConfig;
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

        if (!$this->_storeManager->getStore()->isAdmin()) {
            $dispatchResult = new Magento_Object(array('should_proceed' => true, 'customer_logged_in' => false));
            $this->_eventManager->dispatch('websiterestriction_frontend', array(
                'controller' => $controller, 'result' => $dispatchResult
            ));
            if (!$dispatchResult->getShouldProceed()) {
                return;
            }
            if (!$this->_config->isRestrictionEnabled()) {
                return;
            }
            /* @var $request Magento_Core_Controller_Request_Http */
            $request    = $controller->getRequest();
            /* @var $response Magento_Core_Controller_Response_Http */
            $response   = $controller->getResponse();
            switch ($this->_config->getMode()) {
                // show only landing page with 503 or 200 code
                case Magento_WebsiteRestriction_Model_Mode::ALLOW_NONE:
                    if ($controller->getFullActionName() !== 'restriction_index_stub') {
                        $request->setModuleName('restriction')
                            ->setControllerName('index')
                            ->setActionName('stub')
                            ->setDispatched(false);
                        return;
                    }
                    $httpStatus = $this->_config->getHTTPStatusCode();
                    if (Magento_WebsiteRestriction_Model_Mode::HTTP_503 === $httpStatus) {
                        $response->setHeader('HTTP/1.1', '503 Service Unavailable');
                    }
                    break;

                case Magento_WebsiteRestriction_Model_Mode::ALLOW_REGISTER:
                    // break intentionally omitted

                    //redirect to landing page/login
                case Magento_WebsiteRestriction_Model_Mode::ALLOW_LOGIN:
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
                        if (Magento_WebsiteRestriction_Model_Mode::HTTP_302_LANDING === $restrictionRedirectCode) {
                            $cmsPageViewAction = 'cms_page_view';
                            $allowedActionNames[] = $cmsPageViewAction;
                            $pageIdentifier = $this->_config->getLandingPageCode();
                            // Restrict access to CMS pages too
                            if (!in_array($controller->getFullActionName(), $allowedActionNames)
                                || ($controller->getFullActionName() === $cmsPageViewAction
                                    && $request->getAlias('rewrite_request_path') !== $pageIdentifier)
                            ) {
                                $redirectUrl = Mage::getUrl('', array('_direct' => $pageIdentifier));
                            }
                        } elseif (!in_array($controller->getFullActionName(), $allowedActionNames)) {
                            // to login form
                            $redirectUrl = Mage::getUrl('customer/account/login');
                        }

                        if ($redirectUrl) {
                            $response->setRedirect($redirectUrl);
                            $controller->setFlag('', Magento_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                        }
                        if ($this->_storeConfig->getConfigFlag(
                            Magento_Customer_Helper_Data::XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD
                        )) {
                            $afterLoginUrl = $this->_customerHelper->getDashboardUrl();
                        } else {
                            $afterLoginUrl = Mage::getUrl();
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
     * Make layout load additional handler when in private sales mode
     *
     * @param Magento_Event_Observer $observer
     */
    public function addPrivateSalesLayoutUpdate($observer)
    {
        if (in_array($this->_config->getMode(),
            array(
                Magento_WebsiteRestriction_Model_Mode::ALLOW_REGISTER,
                Magento_WebsiteRestriction_Model_Mode::ALLOW_LOGIN
            ),
            true
        )) {
            $observer->getEvent()->getLayout()->getUpdate()->addHandle('restriction_privatesales_mode');
        }
    }
}
