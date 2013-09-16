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
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_coreConfig;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Customer_Helper_Data $customerData
     * @param Magento_WebsiteRestriction_Helper_Data $websiteRestrictionData
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_Config $coreConfig
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Customer_Helper_Data $customerData,
        Magento_WebsiteRestriction_Helper_Data $websiteRestrictionData,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_Config $coreConfig
    ) {
        $this->_eventManager = $eventManager;
        $this->_customerData = $customerData;
        $this->_websiteRestrictionData = $websiteRestrictionData;
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_coreConfig = $coreConfig;
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

        if (!Mage::app()->getStore()->isAdmin()) {
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
            switch ((int)$this->_coreStoreConfig->getConfig(Magento_WebsiteRestriction_Helper_Data::XML_PATH_RESTRICTION_MODE)) {
                // show only landing page with 503 or 200 code
                case Magento_WebsiteRestriction_Model_Mode::ALLOW_NONE:
                    if ($controller->getFullActionName() !== 'restriction_index_stub') {
                        $request->setModuleName('restriction')
                            ->setControllerName('index')
                            ->setActionName('stub')
                            ->setDispatched(false);
                        return;
                    }
                    $httpStatus = (int)$this->_coreStoreConfig->getConfig(
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
                        $allowedActionNames = array_keys($this->_coreConfig
                            ->getNode(Magento_WebsiteRestriction_Helper_Data::XML_NODE_RESTRICTION_ALLOWED_GENERIC)
                            ->asArray()
                        );
                        if ($this->_customerData->isRegistrationAllowed()) {
                            foreach(array_keys($this->_coreConfig
                                ->getNode(
                                    Magento_WebsiteRestriction_Helper_Data::XML_NODE_RESTRICTION_ALLOWED_REGISTER
                                )
                                ->asArray()) as $fullActionName
                            ) {
                                $allowedActionNames[] = $fullActionName;
                            }
                        }

                        // to specified landing page
                        $restrictionRedirectCode = (int)$this->_coreStoreConfig->getConfig(
                            Magento_WebsiteRestriction_Helper_Data::XML_PATH_RESTRICTION_HTTP_REDIRECT
                        );
                        if (Magento_WebsiteRestriction_Model_Mode::HTTP_302_LANDING === $restrictionRedirectCode) {
                            $cmsPageViewAction = 'cms_page_view';
                            $allowedActionNames[] = $cmsPageViewAction;
                            $pageIdentifier = $this->_coreStoreConfig->getConfig(
                                Magento_WebsiteRestriction_Helper_Data::XML_PATH_RESTRICTION_LANDING_PAGE
                            );
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
                        if ($this->_coreStoreConfig->getConfigFlag(
                            Magento_Customer_Helper_Data::XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD
                        )) {
                            $afterLoginUrl = $this->_customerData->getDashboardUrl();
                        } else {
                            $afterLoginUrl = Mage::getUrl();
                        }
                        Mage::getSingleton('Magento_Core_Model_Session')
                            ->setWebsiteRestrictionAfterLoginUrl($afterLoginUrl);
                    } elseif (Mage::getSingleton('Magento_Core_Model_Session')->hasWebsiteRestrictionAfterLoginUrl()) {
                        $response->setRedirect(
                            Mage::getSingleton('Magento_Core_Model_Session')->getWebsiteRestrictionAfterLoginUrl(true)
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
        if ((!Mage::app()->getStore()->isAdmin()) && $result->getIsAllowed()) {
            $restrictionMode = (int)$this->_coreStoreConfig->getConfig(
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
        if (in_array((int)$this->_coreStoreConfig->getConfig(Magento_WebsiteRestriction_Helper_Data::XML_PATH_RESTRICTION_MODE),
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
