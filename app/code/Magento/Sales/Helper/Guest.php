<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Helper;

/**
 * Sales module base helper
 */
class Guest extends \Magento\Core\Helper\Data
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
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Stdlib\Cookie
     */
    protected $_coreCookie;

    /**
     * @var \Magento\Core\Model\Session
     */
    protected $_session;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Locale $locale
     * @param \Magento\App\State $appState
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Stdlib\Cookie $coreCookie
     * @param \Magento\Core\Model\Session $coreSession
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\App\ViewInterface $view
     * @param bool $dbCompatibleMode
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Locale $locale,
        \Magento\App\State $appState,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Stdlib\Cookie $coreCookie,
        \Magento\Core\Model\Session $coreSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\App\ViewInterface $view,
        $dbCompatibleMode = true
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_customerSession = $customerSession;
        $this->_coreCookie = $coreCookie;
        $this->_session = $coreSession;
        $this->_orderFactory = $orderFactory;
        $this->_view = $view;
        parent::__construct(
            $context,
            $coreStoreConfig,
            $storeManager,
            $locale,
            $appState,
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
            $this->_app->getResponse()->setRedirect($this->_urlBuilder->getUrl('sales/order/history'));
            return false;
        }

        $post = $this->_app->getRequest()->getPost();
        $errors = false;

        /** @var $order \Magento\Sales\Model\Order */
        $order = $this->_orderFactory->create();

        if (empty($post) && !$this->_coreCookie->get($this->_cookieName)) {
            $this->_app->getResponse()->setRedirect($this->_urlBuilder->getUrl('sales/guest/form'));
            return false;
        } elseif (!empty($post) && isset($post['oar_order_id']) && isset($post['oar_type'])) {
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

        $this->_session->addError(
            __('You entered incorrect data. Please try again.')
        );
        $this->_app->getResponse()->setRedirect($this->_urlBuilder->getUrl('sales/guest/form'));
        return false;
    }

    /**
     * Get Breadcrumbs for current controller action
     */
    public function getBreadcrumbs()
    {
        $breadcrumbs = $this->_view->getLayout()->getBlock('breadcrumbs');
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
