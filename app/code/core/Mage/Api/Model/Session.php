<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Api
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Webservice api session
 *
 * @category   Mage
 * @package    Mage_Api
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api_Model_Session extends Mage_Core_Model_Session_Abstract
{
    /**
     * Started session IDs
     *
     * @var array
     */
    public $sessionIds = array();

    /**
     * Current session ID
     *
     * @var string
     */
    protected $_currentSessionId;

    /**
     * Start new session
     *
     * @param string|null $sessionName
     * @return Mage_Api_Model_Session
     */
    public function start($sessionName = null)
    {
        if (null == $sessionName) {
            $sessionName = rand(0, 10000);
        }
        $this->_currentSessionId = md5(time() . $sessionName);
        $this->sessionIds[] = $this->getSessionId();
        return $this;
    }

    /**
     * Init session
     *
     * @param string $namespace
     * @param string $sessionName
     * @return Mage_Api_Model_Session
     */
    public function init($namespace, $sessionName=null)
    {
        if (null === $this->_currentSessionId) {
            $this->start();
        }
        return $this;
    }

    /**
     * Retrieve session Id
     *
     * @return string
     */
    public function getSessionId()
    {
        return $this->_currentSessionId;
    }

    /**
     * Specify session identifier
     *
     * @param   string|null $id
     * @return  Mage_Api_Model_Session
     */
    public function setSessionId($id = null)
    {
        if (null !== $id) {
            $this->_currentSessionId = $id;
        }
        return $this;
    }

    /**
     * Revalidate cookie
     *
     * Unused method
     *
     * @deprecated
     * @return Mage_Api_Model_Session
     */
    public function revalidateCookie()
    {
        return $this;
    }

    /**
     * Logout user by session ID
     *
     * @return bool
     */
    public function clear()
    {
        if (false !== ($sessionId = $this->getSessionId())) {
            try {
                Mage::getModel('api/user')->logoutBySessId($sessionId);
            } catch (Exception $e) {
                return false;
            }
        }
        return true;
    }

    /**
     * Login with api credentials
     *
     * @param string $username
     * @param string $apiKey
     * @return Mage_Api_Model_User|null
     * @throws Mage_Core_Exception
     */
    public function login($username, $apiKey)
    {
        if (empty($username) || empty($apiKey)) {
            return null;
        }

        /** @var $user Mage_Api_Model_User */
        $user = Mage::getModel('api/user');
        $user->setSessid($this->getSessionId())
            ->login($username, $apiKey);

        if ($user->getId() && $user->getIsActive() != '1') {
            Mage::throwException(Mage::helper('api')->__('Your account has been deactivated.'));
        } elseif (!Mage::getModel('api/user')->hasAssigned2Role($user->getId())) {
            Mage::throwException(Mage::helper('api')->__('Access denied.'));
        } else {
            if ($user->getId()) {
                $this->setUser($user);
                $this->setAcl(Mage::getResourceModel('api/acl')->loadAcl());
            } else {
                Mage::throwException(Mage::helper('api')->__('Unable to login.'));
            }
        }

        return $user;
    }

    /**
     * Refresh ACL
     *
     * @param Mage_Api_Model_User|null $user
     * @return Mage_Api_Model_Session
     */
    public function refreshAcl(Mage_Api_Model_User $user = null)
    {
        if (null === $user) {
            $user = $this->getUser();
        }
        if (!$user) {
            return $this;
        }
        if (!$this->getAcl() || $user->getReloadAclFlag()) {
            $this->setAcl(Mage::getResourceModel('api/acl')->loadAcl());
        }
        if ($user->getReloadAclFlag()) {
            $user->unsetData('api_key');
            $user->setReloadAclFlag('0')->save();
        }
        return $this;
    }

    /**
     * Check current user permission on resource and privilege
     *
     * @param   string $resource
     * @param   string $privilege
     * @return  bool
     */
    public function isAllowed($resource, $privilege=null)
    {
        $user = $this->getUser();
        $acl = $this->getAcl();

        if ($user && $acl) {
            try {
                if ($acl->isAllowed($user->getAclRole(), 'all', null)){
                    return true;
                }
            } catch (Exception $e) {}

            try {
                return $acl->isAllowed($user->getAclRole(), $resource, $privilege);
            } catch (Exception $e) {
                return false;
            }
        }
        return false;
    }

    /**
     * Check session expiration
     *
     * @param Mage_Api_Model_User $user
     * @return bool
     */
    public function isSessionExpired(Mage_Api_Model_User $user)
    {
        if (!$user->getId()) {
            return true;
        }
        $timeout = strtotime(now()) - strtotime($user->getLogdate());
        return $timeout > Mage::getStoreConfig('api/config/session_timeout');
    }

    /**
     * Check logged by session id
     *
     * @param bool $id
     * @return bool
     */
    public function isLoggedIn($id = false)
    {
        $userExists = $this->getUser() && $this->getUser()->getId();

        if (!$userExists && $id !== false) {
            return $this->_renewBySessId($id);
        }

        if ($userExists) {
            Mage::register('isSecureArea', true, true);
        }
        return $userExists;
    }

    /**
     * Renew user by session ID if session not expired
     *
     * @param    string $id
     * @return   boolean
     */
    protected function _renewBySessId($id)
    {
        /** @var $user Mage_Api_Model_User */
        $user = Mage::getModel('api/user')->loadBySessId($id);
        if (!$user->getId() || !$user->getSessid()) {
            return false;
        }

        if ($user->getSessid() == $id && !$this->isSessionExpired($user)) {
            $this->setUser($user);
            $this->setAcl(Mage::getResourceModel('api/acl')->loadAcl());

            $user->getResource()->recordLogin($user)
                ->recordSession($user);

            return true;
        }
        return false;
    }

}
