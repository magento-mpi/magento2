<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Backend Auth session model
 *
 * @category    Mage
 * @package     Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Auth_Session extends Mage_Core_Model_Session_Abstract implements Mage_Backend_Model_Auth_StorageInterface
{
    const XML_PATH_SESSION_LIFETIME = 'admin/security/session_lifetime';

    /**
     * Whether it is the first page after successfull login
     *
     * @var boolean
     */
    protected $_isFirstPageAfterLogin;

    /**
     * Access Control List builder
     *
     * @var Mage_Core_Model_Acl_Builder
     */
    protected $_aclBuilder;

    /**
     * Class constructor
     *
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        if (isset($data['aclBuilder'])) {
            $this->_aclBuilder = $data['aclBuilder'];
        } else {
            $areaConfig = Mage::getConfig()->getAreaConfig(Mage::helper('Mage_Backend_Helper_Data')->getAreaCode());
            $this->_aclBuilder = Mage::getModel('Mage_Core_Model_Acl_Builder', array(
                'areaConfig' => $areaConfig,
                'objectFactory' => Mage::getConfig()
            ));
        }
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
     * @return Mage_Backend_Model_Auth_Session
     * @see self::login()
     */
    public function init($namespace, $sessionName = null)
    {
        parent::init($namespace, $sessionName);
        $this->isFirstPageAfterLogin();
        return $this;
    }

    /**
     * Refresh ACL resources stored in session
     *
     * @param  Mage_User_Model_User $user
     * @return Mage_Backend_Model_Auth_Session
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
     * Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed('Mage_Catalog::catalog')
     * Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed('Mage_Catalog::catalog')
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
            } catch (Exception $e) {
                try {
                    if (!$acl->has($resource)) {
                        return $acl->isAllowed($user->getAclRole(), null, $privilege);
                    }
                } catch (Exception $e) { }
            }
        }
        return false;
    }

    /**
     * Delete nodes that have "acl" attribute but value is "not allowed"
     *
     * In any case, the "acl" attribute will be unset
     *
     * @param Varien_Simplexml_Element $xml
     */
    public function filterAclNodes(Varien_Simplexml_Element $xml)
    {
        $limitations = $xml->xpath('//*[@acl]') ?: array();
        foreach ($limitations as $node) {
            if (isset($node['acl'])) {
                if (!$this->isAllowed($node['acl'])) {
                    $node->unsetSelf();
                } else {
                    unset($node['acl']);
                }
            }
        }
    }

    /**
     * Check if user is logged in
     *
     * @return boolean
     */
    public function isLoggedIn()
    {
        $lifetime = Mage::getStoreConfig(self::XML_PATH_SESSION_LIFETIME);
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
        if (is_null($this->_isFirstPageAfterLogin)) {
            $this->_isFirstPageAfterLogin = $this->getData('is_first_visit', true);
        }
        return $this->_isFirstPageAfterLogin;
    }

    /**
     * Setter whether the current/next page should be treated as first page after login
     *
     * @param bool $value
     * @return Mage_Backend_Model_Auth_Session
     */
    public function setIsFirstPageAfterLogin($value)
    {
        $this->_isFirstPageAfterLogin = (bool)$value;
        return $this->setIsFirstVisit($this->_isFirstPageAfterLogin);
    }

    /**
     * Process of configuring of current auth storage when login was performed
     *
     * @return Mage_Backend_Model_Auth_Session
     */
    public function processLogin()
    {
        if ($this->getUser()) {
            $this->renewSession();

            if (Mage::getSingleton('Mage_Adminhtml_Model_Url')->useSecretKey()) {
                Mage::getSingleton('Mage_Adminhtml_Model_Url')->renewSecretUrls();
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
     * @return Mage_Backend_Model_Auth_Session
     */
    public function processLogout()
    {
        $this->unsetAll();
        $this->getCookie()->delete($this->getSessionName());
        return $this;
    }
}
