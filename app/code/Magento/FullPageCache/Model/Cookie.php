<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_FullPageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\FullPageCache\Model;

/**
 * Full page cache cookie model
 */
class Cookie extends \Magento\Stdlib\Cookie
{
    /**
     * Cookie names
     */
    const COOKIE_CUSTOMER           = 'CUSTOMER';
    const COOKIE_CUSTOMER_GROUP     = 'CUSTOMER_INFO';

    const COOKIE_MESSAGE            = 'NEWMESSAGE';
    const COOKIE_CART               = 'CART';
    const COOKIE_COMPARE_LIST       = 'COMPARE';
    const COOKIE_RECENTLY_COMPARED  = 'RECENTLYCOMPARED';
    const COOKIE_WISHLIST           = 'WISHLIST';
    const COOKIE_WISHLIST_ITEMS     = 'WISHLIST_CNT';

    const COOKIE_CUSTOMER_LOGGED_IN = 'CUSTOMER_AUTH';

    /**
     * Sub-processors cookie names
     */
    const COOKIE_CATEGORY_PROCESSOR = 'CATEGORY_INFO';

    /**
     * Cookie to store last visited category id
     */
    const COOKIE_CATEGORY_ID = 'LAST_CATEGORY';

    /**
     * Customer segment ids cookie name
     */
    const CUSTOMER_SEGMENT_IDS = 'CUSTOMER_SEGMENT_IDS';

    /**
     * Cookie name for users who allowed cookie save
     */
    const IS_USER_ALLOWED_SAVE_COOKIE  = 'user_allowed_save_cookie';

    /**
     * Encryption salt value
     *
     * @var string
     */
    protected $_salt;

    /**
     * FPC cache model
     *
     * @var \Magento\FullPageCache\Model\Cache
     */
    protected $_fpcCache;

    /**
     * Core event manager proxy
     *
     * @var \Magento\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\FullPageCache\Model\Cache $fpcCache
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\App\RequestInterface $request,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\FullPageCache\Model\Cache $fpcCache,
        \Magento\Customer\Model\Session $customerSession
    ) {
        parent::__construct($request);
        $this->_eventManager = $eventManager;
        $this->_fpcCache = $fpcCache;
        $this->_customerSession = $customerSession;
    }

    /**
     * Retrieve encryption salt
     *
     * @return null|string
     */
    protected function _getSalt()
    {
        if ($this->_salt === null) {
            $saltCacheId = 'full_page_cache_key';
            $this->_salt = $this->_fpcCache->load($saltCacheId);
            if (!$this->_salt) {
                $this->_salt = md5(microtime() . rand());
                $this->_fpcCache->save($this->_salt, $saltCacheId, array(Processor::CACHE_TAG));
            }
        }
        return $this->_salt;
    }

    /**
     * Set cookie with obscure value
     *
     * @param string $name The cookie name
     * @param string $value The cookie value
     * @param int $period Lifetime period
     * @param string $path
     * @param string $domain
     * @param bool|int|string $secure
     * @param bool|string $httponly
     * @return \Magento\Stdlib\Cookie
     */
    public function setObscure(
        $name, $value, $period = null, $path = null, $domain = null, $secure = null, $httponly = null
    ) {
        $value = md5($this->_getSalt() . $value);
        $period = $period ?: $this->_customerSession->getCookieLifetime();
        $path = $path ?: $this->_customerSession->getCookiePath();
        $domain = $domain ?: $this->_customerSession->getCookieDomain();
        return $this->set($name, $value, $period, $path, $domain, $secure, $httponly);
    }

    /**
     * Keep customer cookies synchronized with customer session
     *
     * @return $this
     */
    public function updateCustomerCookies()
    {
        $customerId = $this->_customerSession->getCustomerId();
        $customerGroupId = $this->_customerSession->getCustomerGroupId();
        if (!$customerId || null === $customerGroupId) {
            $customerCookies = new \Magento\Object();
            $this->_eventManager->dispatch('update_customer_cookies', array('customer_cookies' => $customerCookies));
            if (!$customerId) {
                $customerId = $customerCookies->getCustomerId();
            }
            if (null === $customerGroupId) {
                $customerGroupId = $customerCookies->getCustomerGroupId();
            }
        }

        if ($customerId && null !== $customerGroupId) {
            $this->setObscure(self::COOKIE_CUSTOMER, 'customer_' . $customerId);
            $this->setObscure(self::COOKIE_CUSTOMER_GROUP, 'customer_group_' . $customerGroupId);
            if ($this->_customerSession->isLoggedIn()) {
                $this->setObscure(
                    self::COOKIE_CUSTOMER_LOGGED_IN,
                    'customer_logged_in_' . $this->_customerSession->isLoggedIn()
                );
            } else {
                $this->unsetCookie(self::COOKIE_CUSTOMER_LOGGED_IN);
            }
        } else {
            $this->unsetCookie(self::COOKIE_CUSTOMER);
            $this->unsetCookie(self::COOKIE_CUSTOMER_GROUP);
            $this->unsetCookie(self::COOKIE_CUSTOMER_LOGGED_IN);
        }
        return $this;
    }

    /**
     * Unset cookie
     *
     * @param string $name
     * @return $this
     */
    protected function unsetCookie($name)
    {
        $this->set(
            $name,
            null,
            null,
            $this->_customerSession->getCookiePath(),
            $this->_customerSession->getCookieDomain()
        );
        return $this;
    }

    /**
     * Register viewed product ids in cookie
     *
     * @param int|string|array $productIds
     * @param int $countLimit
     * @param bool $append
     * @return void
     */
    public static function registerViewedProducts($productIds, $countLimit, $append = true)
    {
        if (!is_array($productIds)) {
            $productIds = array($productIds);
        }
        if ($append) {
            if (!empty($_COOKIE[Container\Viewedproducts::COOKIE_NAME])) {
                $cookieIds = $_COOKIE[Container\Viewedproducts::COOKIE_NAME];
                $cookieIds = explode(',', $cookieIds);
            } else {
                $cookieIds = array();
            }
            array_splice($cookieIds, 0, 0, $productIds);  // append to the beginning
        } else {
            $cookieIds = $productIds;
        }
        $cookieIds = array_unique($cookieIds);
        $cookieIds = array_slice($cookieIds, 0, $countLimit);
        $cookieIds = implode(',', $cookieIds);
        setcookie(Container\Viewedproducts::COOKIE_NAME, $cookieIds, 0, '/');
    }

    /**
     * Set catalog cookie
     *
     * @param string $value
     * @return void
     */
    public static function setCategoryCookieValue($value)
    {
        setcookie(self::COOKIE_CATEGORY_PROCESSOR, $value, 0, '/');
    }

    /**
     * Get catalog cookie
     *
     * @static
     * @return bool
     */
    public static function getCategoryCookieValue()
    {
        return (isset($_COOKIE[self::COOKIE_CATEGORY_PROCESSOR])) ? $_COOKIE[self::COOKIE_CATEGORY_PROCESSOR] : false;
    }

    /**
     * Set cookie with visited category id
     *
     * @param int $categoryId
     * @return void
     */
    public static function setCategoryViewedCookieValue($categoryId)
    {
        setcookie(self::COOKIE_CATEGORY_ID, $categoryId, 0, '/');
    }
}
