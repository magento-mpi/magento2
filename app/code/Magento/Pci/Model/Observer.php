<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pci
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Pci observer model
 *
 * Implements hashes upgrading
 */
class Magento_Pci_Model_Observer
{
    const ADMIN_USER_LOCKED = 243;

    /**
     * @var Magento_AuthorizationInterface
     */
    protected $_authorization;

    /**
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData = null;

    /**
     * @var Magento_Core_Model_Store_Config
     */
    protected $_storeConfig;

    /**
     * @var Magento_Pci_Model_Resource_Admin_User
     *
     */
    protected $_userResource;
    /**
     * @var Magento_Backend_Model_Url
     */
    protected $_url;

    /**
     * @var Magento_Adminhtml_Model_Session
     */
    protected $_session;

    /**
     * @var Magento_Backend_Model_Auth_Session
     */
    protected $_authSession;

    /**
     * @var Magento_User_Model_UserFactory
     */
    protected $_userFactory;

    /**
     * @var Magento_Api_Model_UserFactory
     */
    protected $_apiUserFactory;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_AuthorizationInterface $authorization
     * @param Magento_Core_Model_Store_Config $storeConfig
     * @param Magento_Pci_Model_Resource_Admin_User $userResource
     * @param Magento_Backend_Model_Url $url
     * @param Magento_Adminhtml_Model_Session $session
     * @param Magento_Backend_Model_Auth_Session $authSession
     * @param Magento_User_Model_UserFactory $userFactory
     * @param Magento_Api_Model_UserFactory $apiUserFactory
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_AuthorizationInterface $authorization,
        Magento_Core_Model_Store_Config $storeConfig,
        Magento_Pci_Model_Resource_Admin_User $userResource,
        Magento_Backend_Model_Url $url,
        Magento_Adminhtml_Model_Session $session,
        Magento_Backend_Model_Auth_Session $authSession,
        Magento_User_Model_UserFactory $userFactory,
        Magento_Api_Model_UserFactory $apiUserFactory
    ) {
        $this->_coreData = $coreData;
        $this->_authorization = $authorization;
        $this->_storeConfig = $storeConfig;
        $this->_userResource = $userResource;
        $this->_url = $url;
        $this->_session = $session;
        $this->_authSession = $authSession;
        $this->_userFactory = $userFactory;
        $this->_apiUserFactory = $apiUserFactory;
    }

    /**
     * Admin locking and password hashing upgrade logic implementation
     *
     * @param Magento_Event_Observer $observer
     * @throws Magento_Core_Exception
     */
    public function adminAuthenticate($observer)
    {
        $password = $observer->getEvent()->getPassword();
        $user     = $observer->getEvent()->getUser();
        $resource = $this->_userResource;
        $authResult = $observer->getEvent()->getResult();

        // update locking information regardless whether user locked or not
        if ((!$authResult) && ($user->getId())) {
            $now = time();
            $lockThreshold = $this->getAdminLockThreshold();
            $maxFailures = (int)$this->_storeConfig->getConfig('admin/security/lockout_failures');
            if (!($lockThreshold && $maxFailures)) {
                return;
            }
            $failuresNum = (int)$user->getFailuresNum() + 1;
            if ($firstFailureDate = $user->getFirstFailure()) {
                $firstFailureDate = new Zend_Date($firstFailureDate, Magento_Date::DATETIME_INTERNAL_FORMAT);
                $firstFailureDate = $firstFailureDate->toValue();
            }

            $updateFirstFailureDate = false;
            $updateLockExpires      = false;
            // set first failure date when this is first failure or last first failure expired
            if (1 === $failuresNum || !$firstFailureDate || (($now - $firstFailureDate) > $lockThreshold)) {
                $updateFirstFailureDate = $now;
            }
            // otherwise lock user
            elseif ($failuresNum >= $maxFailures) {
                $updateLockExpires = $now + $lockThreshold;
            }
            $resource->updateFaiure($user, $updateLockExpires, $updateFirstFailureDate);
        }

        // check whether user is locked
        if ($lockExpires = $user->getLockExpires()) {
            $lockExpires = new Zend_Date($lockExpires, Magento_Date::DATETIME_INTERNAL_FORMAT);
            $lockExpires = $lockExpires->toValue();
            if ($lockExpires > time()) {
                throw new Magento_Core_Exception(
                    __('This account is locked.'),
                    self::ADMIN_USER_LOCKED
                );
            }
        }

        if (!$authResult) {
            return;
        }

        $resource->unlock($user->getId());

        /**
         * Check whether the latest password is expired
         * Side-effect can be when passwords were changed with different lifetime configuration settings
         */
        $latestPassword = $this->_userResource->getLatestPassword($user->getId());
        if ($latestPassword) {
            if ($this->_isLatestPasswordExpired($latestPassword)) {
                if ($this->isPasswordChangeForced()) {
                    $message = __('It\'s time to change your password.');
                } else {
                    $myAccountUrl = $this->_url->getUrl('adminhtml/system_account/');
                    $message = __('It\'s time to <a href="%1">change your password</a>.', $myAccountUrl);
                }
                $this->_session->addNotice($message);
                $message = $this->_session->getMessages()->getLastAddedMessage();
                if ($message) {
                    $message->setIdentifier('magento_pci_password_expired')->setIsSticky(true);
                    $this->_authSession->setPciAdminUserIsPasswordExpired(true);
                }
            }
        }

        // upgrade admin password
        if (!$this->_coreData->getEncryptor()->validateHashByVersion($password, $user->getPassword())) {
            $this->_userFactory->create()->load($user->getId())
                ->setNewPassword($password)->setForceNewPassword(true)
                ->save();
        }
    }

    /**
     * Check if latest password is expired
     *
     * @param array $latestPassword
     * @return bool
     */
    protected function _isLatestPasswordExpired($latestPassword)
    {
        if (!isset($latestPassword['expires'])) {
            return false;
        }

        if ($this->getAdminPasswordLifetime() == 0) {
            return false;
        }

        return (int)$latestPassword['expires'] < time();
    }

    /**
     * Upgrade API key hash when api user has logged in
     *
     * @param Magento_Event_Observer $observer
     */
    public function upgradeApiKey($observer)
    {
        $apiKey = $observer->getEvent()->getApiKey();
        $model  = $observer->getEvent()->getModel();
        if (!$this->_coreData->getEncryptor()->validateHashByVersion($apiKey, $model->getApiKey())) {
            $this->_apiUserFactory->create()->load($model->getId())->setNewApiKey($apiKey)->save();
        }
    }

    /**
     * Upgrade customer password hash when customer has logged in
     *
     * @param Magento_Event_Observer $observer
     */
    public function upgradeCustomerPassword($observer)
    {
        $password = $observer->getEvent()->getPassword();
        $model    = $observer->getEvent()->getModel();
        if (!$this->_coreData->getEncryptor()->validateHashByVersion($password, $model->getPassword())) {
            $model->changePassword($password, false);
        }
    }

    /**
     * Harden admin password change.
     *
     * New password must be minimum 7 chars length and include alphanumeric characters
     * The password is compared to at least last 4 previous passwords to prevent setting them again
     *
     * @param Magento_Event_Observer $observer
     * @throws Magento_Core_Exception
     */
    public function checkAdminPasswordChange($observer)
    {
        /* @var $user Magento_User_Model_User */
        $user = $observer->getEvent()->getObject();

        if ($user->getNewPassword()) {
            $password = $user->getNewPassword();
        } else {
            $password = $user->getPassword();
        }

        if ($password && !$user->getForceNewPassword() && $user->getId()) {
            if ($this->_coreData->validateHash($password, $user->getOrigData('password'))) {
                throw new Magento_Core_Exception(
                    __('Sorry, but this password has already been used. Please create another.')
                );
            }

            // check whether password was used before
            $resource = $this->_userResource;
            $passwordHash = $this->_coreData->getHash($password, false);
            foreach ($resource->getOldPasswords($user) as $oldPasswordHash) {
                if ($passwordHash === $oldPasswordHash) {
                    throw new Magento_Core_Exception(
                        __('Sorry, but this password has already been used. Please create another.')
                    );
                }
            }
        }
    }

    /**
     * Save new admin password
     *
     * @param Magento_Event_Observer $observer
     */
    public function trackAdminNewPassword($observer)
    {
        /* @var $user Magento_User_Model_User */
        $user = $observer->getEvent()->getObject();
        if ($user->getId()) {
            $password = $user->getNewPassword();
            $passwordLifetime = $this->getAdminPasswordLifetime();
            if ($passwordLifetime && $password && !$user->getForceNewPassword()) {
                $resource = $this->_userResource;
                $passwordHash = $this->_coreData->getHash($password, false);
                $resource->trackPassword($user, $passwordHash, $passwordLifetime);
                $this->_session->getMessages()->deleteMessageByIdentifier('magento_pci_password_expired');
                $this->_authSession->unsPciAdminUserIsPasswordExpired();
            }
        }
    }

    /**
     * Get admin lock threshold from configuration
     *
     * @return int
     */
    public function getAdminLockThreshold()
    {
        return 60 * (int)$this->_storeConfig->getConfig('admin/security/lockout_threshold');
    }

    /**
     * Get admin password lifetime
     *
     * @return int
     */
    public function getAdminPasswordLifetime()
    {
        return 86400 * (int)$this->_storeConfig->getConfig('admin/security/password_lifetime');
    }

    /**
     * Force admin to change password
     *
     * @param Magento_Event_Observer $observer
     */
    public function forceAdminPasswordChange($observer)
    {
        if (!$this->isPasswordChangeForced()) {
            return;
        }
        $session = $this->_authSession;
        if (!$session->isLoggedIn()) {
            return;
        }
        $actionList = array('adminhtml_system_account_index', 'adminhtml_system_account_save',
            'adminhtml_auth_logout');
        $controller = $observer->getEvent()->getControllerAction();
        if ($this->_authSession->getPciAdminUserIsPasswordExpired()) {
            if (!in_array($controller->getFullActionName(), $actionList)) {
                if ($this->_authorization->isAllowed('Magento_Adminhtml::myaccount')) {
                    $controller->getResponse()->setRedirect(
                        $this->_url->getUrl('adminhtml/system_account/')
                    );
                    $controller->setFlag('', Magento_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                    $controller->setFlag('', Magento_Core_Controller_Varien_Action::FLAG_NO_POST_DISPATCH, true);
                } else {
                    /*
                     * if admin password is expired and access to 'My Account' page is denied
                     * than we need to do force logout with error message
                     */
                    $this->_authSession->unsetAll();
                    $this->_session->unsetAll();
                    $this->_session->addError(
                        __('Your password has expired; please contact your administrator.')
                    );
                    $controller->getRequest()->setDispatched(false);
                }
            }
        }
    }

    /**
     * Check whether password change is forced
     *
     * @return bool
     */
    public function isPasswordChangeForced()
    {
        return (bool)(int)$this->_storeConfig->getConfig('admin/security/password_is_forced');
    }
}
