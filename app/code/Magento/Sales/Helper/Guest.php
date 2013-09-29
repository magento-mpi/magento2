<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales module base helper
 */
class Magento_Sales_Helper_Guest extends Magento_Core_Helper_Data
{
    /**
     * Cookie params
     */
    protected $_cookieName  = 'guest-view';

    /**
     * @var int
     */
    protected $_lifeTime    = 600;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry;

    /**
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * @var Magento_Core_Model_Cookie
     */
    protected $_coreCookie;

    /**
     * @var Magento_Core_Model_App
     */
    protected $_coreApp;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_Core_Model_Session
     */
    protected $_coreSession;

    /**
     * @var Magento_Sales_Model_OrderFactory
     */
    protected $_orderFactory;

    /**
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Helper_Http $coreHttp
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Model_Locale $locale
     * @param Magento_Core_Model_Date $dateModel
     * @param Magento_Core_Model_App_State $appState
     * @param Magento_Core_Model_Encryption $encryptor
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Core_Model_Cookie $coreCookie
     * @param Magento_Core_Model_App $coreApp
     * @param Magento_Core_Model_Session $coreSession
     * @param Magento_Sales_Model_OrderFactory $orderFactory
     * @param bool $dbCompatibleMode
     */
    public function __construct(
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Helper_Http $coreHttp,
        Magento_Core_Model_Config $config,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_Locale $locale,
        Magento_Core_Model_Date $dateModel,
        Magento_Core_Model_App_State $appState,
        Magento_Core_Model_Encryption $encryptor,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Customer_Model_Session $customerSession,
        Magento_Core_Model_Cookie $coreCookie,
        Magento_Core_Model_App $coreApp,
        Magento_Core_Model_Session $coreSession,
        Magento_Sales_Model_OrderFactory $orderFactory,
        $dbCompatibleMode = true
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_customerSession = $customerSession;
        $this->_coreCookie = $coreCookie;
        $this->_coreApp = $coreApp;
        $this->_coreSession = $coreSession;
        $this->_orderFactory = $orderFactory;
        parent::__construct(
            $eventManager,
            $coreHttp,
            $context,
            $config,
            $coreStoreConfig,
            $storeManager,
            $locale,
            $dateModel,
            $appState,
            $encryptor,
            $dbCompatibleMode
        );
    }

    /**
     * Try to load valid order by $_POST or $_COOKIE
     *
     * @return bool|null
     */
    public function loadValidOrder()
    {
        if ($this->_customerSession->isLoggedIn()) {
            $this->_coreApp->getResponse()->setRedirect($this->_urlBuilder->getUrl('sales/order/history'));
            return false;
        }

        $post = $this->_coreApp->getRequest()->getPost();
        $errors = false;

        /** @var $order Magento_Sales_Model_Order */
        $order = $this->_orderFactory->create();

        if (empty($post) && !$this->_coreCookie->get($this->_cookieName)) {
            $this->_coreApp->getResponse()->setRedirect($this->_urlBuilder->getUrl('sales/guest/form'));
            return false;
        } elseif (!empty($post) && isset($post['oar_order_id']) && isset($post['oar_type']))  {
            $type           = $post['oar_type'];
            $incrementId    = $post['oar_order_id'];
            $lastName       = $post['oar_billing_lastname'];
            $email          = $post['oar_email'];
            $zip            = $post['oar_zip'];

            if (empty($incrementId) || empty($lastName) || empty($type) || (!in_array($type, array('email', 'zip')))
                || ($type == 'email' && empty($email)) || ($type == 'zip' && empty($zip))) {
                $errors = true;
            }

            if (!$errors) {
                $order->loadByIncrementId($incrementId);
            }

            if ($order->getId()) {
                $billingAddress = $order->getBillingAddress();
                if ((strtolower($lastName) != strtolower($billingAddress->getLastname()))
                    || ($type == 'email'
                        && strtolower($email) != strtolower($billingAddress->getEmail()))
                    || ($type == 'zip'
                        && (strtolower($zip) != strtolower($billingAddress->getPostcode())))
                ) {
                    $errors = true;
                }
            } else {
                $errors = true;
            }

            if (!$errors) {
                $toCookie = base64_encode($order->getProtectCode());
                $this->_coreCookie->set($this->_cookieName, $toCookie, $this->_lifeTime, '/');
            }
        } elseif ($this->_coreCookie->get($this->_cookieName)) {
            $fromCookie     = $this->_coreCookie->get($this->_cookieName);
            $protectCode    = base64_decode($fromCookie);

            if (!empty($protectCode)) {
                $order->loadByAttribute('protect_code', $protectCode);

                $this->_coreCookie->renew($this->_cookieName, $this->_lifeTime, '/');
            } else {
                $errors = true;
            }
        }

        if (!$errors && $order->getId()) {
            $this->_coreRegistry->register('current_order', $order);
            return true;
        }

        $this->_coreSession->addError(
            __('You entered incorrect data. Please try again.')
        );
        $this->_coreApp->getResponse()->setRedirect($this->_urlBuilder->getUrl('sales/guest/form'));
        return false;
    }

    /**
     * Get Breadcrumbs for current controller action
     *
     * @param  Magento_Core_Controller_Front_Action $controller
     */
    public function getBreadcrumbs($controller)
    {
        $breadcrumbs = $controller->getLayout()->getBlock('breadcrumbs');
        $breadcrumbs->addCrumb(
            'home',
            array(
                'label' => __('Home'),
                'title' => __('Go to Home Page'),
                'link'  => $this->_storeManager->getStore()->getBaseUrl()
            )
        );
        $breadcrumbs->addCrumb(
            'cms_page',
            array(
                'label' => __('Order Information'),
                'title' => __('Order Information')
            )
        );
    }

}
