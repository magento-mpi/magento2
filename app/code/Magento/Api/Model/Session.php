<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Api
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Webservice api session
 *
 * @category   Magento
 * @package    Magento_Api
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Api_Model_Session extends Magento_Core_Model_Session_Abstract
{
    public $sessionIds = array();
    protected $_currentSessId = null;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * Constructor
     *
     * By default is looking for first argument as array and assigns it as object
     * attributes This behavior may change in child classes
     *
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Logger $logger,
        Magento_Core_Model_Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($logger, $data);
    }

    public function start($sessionName = null)
    {
        $this->_currentSessId = md5(time() . uniqid('', true) . $sessionName);
        $this->sessionIds[] = $this->getSessionId();
        return $this;
    }

    public function init($namespace, $sessionName = null)
    {
        if (is_null($this->_currentSessId)) {
            $this->start();
        }
        return $this;
    }

    public function getSessionId()
    {
        return $this->_currentSessId;
    }

    public function setSessionId($sessId = null)
    {
        if (!is_null($sessId)) {
            $this->_currentSessId = $sessId;
        }
        return $this;
    }

    public function revalidateCookie()
    {
        // In api we don't use cookies
    }

    public function clear()
    {
        $sessId = $this->getSessionId();
        if ($sessId) {
            try {
                Mage::getModel('Magento_Api_Model_User')->logoutBySessId($sessId);
            } catch (Exception $e) {
                return false;
            }
        }
        return true;
    }

    public function login($username, $apiKey)
    {
        $user = Mage::getModel('Magento_Api_Model_User')
            ->setSessid($this->getSessionId())
            ->login($username, $apiKey);

        if ($user->getId() && $user->getIsActive() != '1') {
            Mage::throwException(__('Your account has been deactivated.'));
        } elseif (!Mage::getModel('Magento_Api_Model_User')->hasAssigned2Role($user->getId())) {
            Mage::throwException(__('Access denied'));
        } else {
            if ($user->getId()) {
                $this->setUser($user);
                $this->setAcl(Mage::getResourceModel('Magento_Api_Model_Resource_Acl')->loadAcl());
            } else {
                Mage::throwException(__('Unable to login'));
            }
        }

        return $user;
    }

    public function refreshAcl($user = null)
    {
        if (is_null($user)) {
            $user = $this->getUser();
        }
        if (!$user) {
            return $this;
        }
        if (!$this->getAcl() || $user->getReloadAclFlag()) {
            $this->setAcl(Mage::getResourceModel('Magento_Api_Model_Resource_Acl')->loadAcl());
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
                if ($acl->isAllowed($user->getAclRole(), 'all', null)) {
                    return true;
                }
            } catch (Exception $e) {
            }

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
     * @return  boolean
     */
    public function isSessionExpired($user)
    {
        if (!$user->getId()) {
            return true;
        }
        $timeout = strtotime(now()) - strtotime($user->getLogdate());
        return $timeout > Mage::getStoreConfig('api/config/session_timeout');
    }


    public function isLoggedIn($sessId = false)
    {
        $userExists = $this->getUser() && $this->getUser()->getId();

        if (!$userExists && $sessId !== false) {
            return $this->_renewBySessId($sessId);
        }

        if ($userExists) {
            $this->_coreRegistry->register('isSecureArea', true, true);
        }
        return $userExists;
    }

    /**
     * Renew user by session ID if session not expired
     *
     * @param    string $sessId
     * @return  boolean
     */
    protected function _renewBySessId ($sessId)
    {
        $user = Mage::getModel('Magento_Api_Model_User')->loadBySessId($sessId);
        if (!$user->getId() || !$user->getSessid()) {
            return false;
        }

        if ($user->getSessid() == $sessId && !$this->isSessionExpired($user)) {
            $this->setUser($user);
            $this->setAcl(Mage::getResourceModel('Magento_Api_Model_Resource_Acl')->loadAcl());

            $user->getResource()->recordLogin($user)
                ->recordSession($user);

            return true;
        }
        return false;
    }

}
