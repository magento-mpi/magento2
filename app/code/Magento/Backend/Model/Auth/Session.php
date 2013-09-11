<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Backend Auth session model
 *
 * @category    Magento
 * @package     Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Model\Auth;

class Session
    extends \Magento\Core\Model\Session\AbstractSession
    implements \Magento\Backend\Model\Auth\StorageInterface
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
     * @param \Magento\Acl\Builder $aclBuilder
     */
    public function __construct(\Magento\Acl\Builder $aclBuilder)
    {
        $this->_aclBuilder = $aclBuilder;
        $this->init('admin');
    }

    /**
     * Pull out information from session whether there is currently the first page after log in
     *
     * The idea is to set this value on login(), then redirect happens,
     * after that on next request the value is grabbed once the session is initialized
     * Since the session is used as a singleton, the value will be in $_isFirstPageAfterLogin until the end of request,
     * unless it is reset intentionally from somewhere
     *
     * @param string $namespace
     * @param string $sessionName
     * @return \Magento\Backend\Model\Auth\Session
     * @see self::login()
     */
    public function init($namespace, $sessionName = null)
    {
        parent::init($namespace, $sessionName);
        // @todo implement solution that keeps is_first_visit flag in session during redirects
        return $this;
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
        $lifetime = \Mage::getStoreConfig(self::XML_PATH_SESSION_LIFETIME);
        $currentTime = time();

        /* Validate admin session lifetime that should be more than 60 seconds */
        if ($lifetime >= 60 && ($this->getUpdatedAt() < $currentTime - $lifetime)) {
            return false;
        }

        if ($this->getUser() && $this->getUser()->getId()) {
            $this->setUpdatedAt($currentTime);
            return true;
        }
        return false;
    }

    /**
     * Check if it is the first page after successfull login
     *
     * @return boolean
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
            $this->renewSession();

            if (\Mage::getSingleton('Magento\Backend\Model\Url')->useSecretKey()) {
                \Mage::getSingleton('Magento\Backend\Model\Url')->renewSecretUrls();
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
        $this->unsetAll();
        $this->getCookie()->delete($this->getSessionName());
        return $this;
    }
}
