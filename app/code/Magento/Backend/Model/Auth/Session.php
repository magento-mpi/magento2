<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Auth;

/**
 * Backend Auth session model
 *
 * @method \Magento\User\Model\User|null getUser()
 * @method \Magento\Backend\Model\Auth\Session setUser(\Magento\User\Model\User $value)
 * @method \Magento\Acl|null getAcl()
 * @method \Magento\Backend\Model\Auth\Session setAcl(\Magento\Acl $value)
 * @method int getUpdatedAt()
 * @method \Magento\Backend\Model\Auth\Session setUpdatedAt(int $value)
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @todo implement solution that keeps is_first_visit flag in session during redirects
 */
class Session extends \Magento\Session\SessionManager implements \Magento\Backend\Model\Auth\StorageInterface
{
    const XML_PATH_SESSION_LIFETIME = 'admin/security/session_lifetime';

    /**
     * Whether it is the first page after successfull login
     *
     * @var boolean
     */
    protected $_isFirstAfterLogin;

    /**
     * Access Control List builder
     *
     * @var \Magento\Acl\Builder
     */
    protected $_aclBuilder;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrl;

    /**
     * @var \Magento\Backend\App\ConfigInterface
     */
    protected $_config;

    /**
     * @var \Magento\Stdlib\Cookie
     */
    protected $_cookie;

    /**
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\Session\SidResolverInterface $sidResolver
     * @param \Magento\Session\Config\ConfigInterface $sessionConfig
     * @param \Magento\Session\SaveHandlerInterface $saveHandler
     * @param \Magento\Session\ValidatorInterface $validator
     * @param \Magento\Session\StorageInterface $storage
     * @param \Magento\Acl\Builder $aclBuilder
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param \Magento\Backend\App\ConfigInterface $config
     * @param \Magento\Stdlib\Cookie $cookie
     */
    public function __construct(
        \Magento\App\RequestInterface $request,
        \Magento\Session\SidResolverInterface $sidResolver,
        \Magento\Session\Config\ConfigInterface $sessionConfig,
        \Magento\Session\SaveHandlerInterface $saveHandler,
        \Magento\Session\ValidatorInterface $validator,
        \Magento\Session\StorageInterface $storage,
        \Magento\Acl\Builder $aclBuilder,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Backend\App\ConfigInterface $config,
        \Magento\Stdlib\Cookie $cookie
    ) {
        $this->_config = $config;
        $this->_aclBuilder = $aclBuilder;
        $this->_backendUrl = $backendUrl;
        $this->_cookie = $cookie;
        parent::__construct($request, $sidResolver, $sessionConfig, $saveHandler, $validator, $storage);
        $this->start();
    }

    /**
     * Refresh ACL resources stored in session
     *
     * @param  \Magento\User\Model\User $user
     * @return \Magento\Backend\Model\Auth\Session
     */
    public function refreshAcl($user = null)
    {
        if (is_null($user)) {
            $user = $this->getUser();
        }
        if (!$user) {
            return $this;
        }
        if (!$this->getAcl() || $user->getReloadAclFlag()) {
            $this->setAcl($this->_aclBuilder->getAcl());
        }
        if ($user->getReloadAclFlag()) {
            $user->unsetData('password');
            $user->setReloadAclFlag('0')->save();
        }
        return $this;
    }

    /**
     * Check current user permission on resource and privilege
     *
     * @param   string $resource
     * @param   string $privilege
     * @return  boolean
     */
    public function isAllowed($resource, $privilege = null)
    {
        $user = $this->getUser();
        $acl = $this->getAcl();

        if ($user && $acl) {
            try {
                return $acl->isAllowed($user->getAclRole(), $resource, $privilege);
            } catch (\Exception $e) {
                try {
                    if (!$acl->has($resource)) {
                        return $acl->isAllowed($user->getAclRole(), null, $privilege);
                    }
                } catch (\Exception $e) {
                }
            }
        }
        return false;
    }

    /**
     * Check if user is logged in
     *
     * @return boolean
     */
    public function isLoggedIn()
    {
        $lifetime = $this->_config->getValue(self::XML_PATH_SESSION_LIFETIME);
        $currentTime = time();

        /* Validate admin session lifetime that should be more than 60 seconds */
        if ($lifetime >= 60 && $this->getUpdatedAt() < $currentTime - $lifetime) {
            return false;
        }

        if ($this->getUser() && $this->getUser()->getId()) {
            return true;
        }
        return false;
    }

    /**
     * Set session UpdatedAt to current time and update cookie expiration time
     *
     * @return void
     */
    public function prolong()
    {
        $lifetime = $this->_config->getValue(self::XML_PATH_SESSION_LIFETIME);
        $currentTime = time();

        $this->setUpdatedAt($currentTime);
        $cookieValue = $this->_cookie->get($this->getName());
        if ($cookieValue) {
            $this->_cookie->set(
                $this->getName(),
                $cookieValue,
                $lifetime,
                $this->sessionConfig->getCookiePath(),
                $this->sessionConfig->getCookieDomain(),
                $this->sessionConfig->getCookieSecure(),
                $this->sessionConfig->getCookieHttpOnly()
            );
        }
    }

    /**
     * Check if it is the first page after successfull login
     *
     * @return bool
     */
    public function isFirstPageAfterLogin()
    {
        if (is_null($this->_isFirstAfterLogin)) {
            $this->_isFirstAfterLogin = $this->getData('is_first_visit', true);
        }
        return $this->_isFirstAfterLogin;
    }

    /**
     * Setter whether the current/next page should be treated as first page after login
     *
     * @param bool $value
     * @return \Magento\Backend\Model\Auth\Session
     */
    public function setIsFirstPageAfterLogin($value)
    {
        $this->_isFirstAfterLogin = (bool)$value;
        return $this->setIsFirstVisit($this->_isFirstAfterLogin);
    }

    /**
     * Process of configuring of current auth storage when login was performed
     *
     * @return \Magento\Backend\Model\Auth\Session
     */
    public function processLogin()
    {
        if ($this->getUser()) {
            $this->regenerateId();

            if ($this->_backendUrl->useSecretKey()) {
                $this->_backendUrl->renewSecretUrls();
            }

            $this->setIsFirstPageAfterLogin(true);
            $this->setAcl($this->_aclBuilder->getAcl());
            $this->setUpdatedAt(time());
        }
        return $this;
    }

    /**
     * Process of configuring of current auth storage when logout was performed
     *
     * @return \Magento\Backend\Model\Auth\Session
     */
    public function processLogout()
    {
        $this->destroy();
        return $this;
    }

    /**
     * Skip path validation in backend area
     *
     * @param string $path
     * @return bool
     */
    public function isValidForPath($path)
    {
        return true;
    }
}
