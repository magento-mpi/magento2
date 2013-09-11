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
namespace Magento\Pci\Model;

class Observer
{
    const ADMIN_USER_LOCKED = 243;

    /**
     * @var \Magento\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @param \Magento\AuthorizationInterface $authorization
     */
    public function __construct(\Magento\AuthorizationInterface $authorization)
    {
        $this->_authorization = $authorization;
    }

    /**
     * Admin locking and password hashing upgrade logic implementation
     *
     * @param \Magento\Event\Observer $observer
     * @throws \Magento\Core\Exception
     */
    public function adminAuthenticate($observer)
    {
        $password = $observer->getEvent()->getPassword();
        $user     = $observer->getEvent()->getUser();
        $resource = \Mage::getResourceSingleton('Magento\Pci\Model\Resource\Admin\User');
        $authResult = $observer->getEvent()->getResult();

        // update locking information regardless whether user locked or not
        if ((!$authResult) && ($user->getId())) {
            $now = time();
            $lockThreshold = $this->getAdminLockThreshold();
            $maxFailures = (int)\Mage::getStoreConfig('admin/security/lockout_failures');
            if (!($lockThreshold && $maxFailures)) {
                return;
            }
            $failuresNum = (int)$user->getFailuresNum() + 1;
            if ($firstFailureDate = $user->getFirstFailure()) {
                $firstFailureDate = new \Zend_Date($firstFailureDate, \Magento\Date::DATETIME_INTERNAL_FORMAT);
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
            $lockExpires = new \Zend_Date($lockExpires, \Magento\Date::DATETIME_INTERNAL_FORMAT);
            $lockExpires = $lockExpires->toValue();
            if ($lockExpires > time()) {
                throw new \Magento\Core\Exception(
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
        $latestPassword = \Mage::getResourceSingleton('Magento\Pci\Model\Resource\Admin\User')->getLatestPassword($user->getId());
        if ($latestPassword) {
            if ($this->_isLatestPasswordExpired($latestPassword)) {
                if ($this->isPasswordChangeForced()) {
                    $message = __('It\'s time to change your password.');
                } else {
                    $myAccountUrl = \Mage::getSingleton('Magento\Backend\Model\Url')->getUrl('adminhtml/system_account/');
                    $message = __('It\'s time to <a href="%1">change your password</a>.', $myAccountUrl);
                }
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addNotice($message);
                if ($message = \Mage::getSingleton('Magento\Adminhtml\Model\Session')->getMessages()->getLastAddedMessage()) {
                    $message->setIdentifier('magento_pci_password_expired')->setIsSticky(true);
                    \Mage::getSingleton('Magento\Backend\Model\Auth\Session')->setPciAdminUserIsPasswordExpired(true);
                }
            }
        }

        // upgrade admin password
        if (!\Mage::helper('Magento\Core\Helper\Data')->getEncryptor()->validateHashByVersion($password, $user->getPassword())) {
            \Mage::getModel('Magento\User\Model\User')->load($user->getId())
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
     * @param \Magento\Event\Observer $observer
     */
    public function upgradeApiKey($observer)
    {
        $apiKey = $observer->getEvent()->getApiKey();
        $model  = $observer->getEvent()->getModel();
        if (!\Mage::helper('Magento\Core\Helper\Data')->getEncryptor()->validateHashByVersion($apiKey, $model->getApiKey())) {
            \Mage::getModel('Magento\Api\Model\User')->load($model->getId())->setNewApiKey($apiKey)->save();
        }
    }

    /**
     * Upgrade customer password hash when customer has logged in
     *
     * @param \Magento\Event\Observer $observer
     */
    public function upgradeCustomerPassword($observer)
    {
        $password = $observer->getEvent()->getPassword();
        $model    = $observer->getEvent()->getModel();
        if (!\Mage::helper('Magento\Core\Helper\Data')->getEncryptor()->validateHashByVersion($password, $model->getPassword())) {
            $model->changePassword($password, false);
        }
    }

    /**
     * Harden admin password change.
     *
     * New password must be minimum 7 chars length and include alphanumeric characters
     * The password is compared to at least last 4 previous passwords to prevent setting them again
     *
     * @param \Magento\Event\Observer $observer
     * @throws \Magento\Core\Exception
     */
    public function checkAdminPasswordChange($observer)
    {
        /* @var $user \Magento\User\Model\User */
        $user = $observer->getEvent()->getObject();

        if ($user->getNewPassword()) {
            $password = $user->getNewPassword();
        } else {
            $password = $user->getPassword();
        }

        if ($password && !$user->getForceNewPassword() && $user->getId()) {
            if (\Mage::helper('Magento\Core\Helper\Data')->validateHash($password, $user->getOrigData('password'))) {
                \Mage::throwException(__('Sorry, but this password has already been used. Please create another.'));
            }

            // check whether password was used before
            $resource     = \Mage::getResourceSingleton('Magento\Pci\Model\Resource\Admin\User');
            $passwordHash = \Mage::helper('Magento\Core\Helper\Data')->getHash($password, false);
            foreach ($resource->getOldPasswords($user) as $oldPasswordHash) {
                if ($passwordHash === $oldPasswordHash) {
                    \Mage::throwException(__('Sorry, but this password has already been used. Please create another.'));
                }
            }
        }
    }

    /**
     * Save new admin password
     *
     * @param \Magento\Event\Observer $observer
     */
    public function trackAdminNewPassword($observer)
    {
        /* @var $user \Magento\User\Model\User */
        $user = $observer->getEvent()->getObject();
        if ($user->getId()) {
            $password = $user->getNewPassword();
            $passwordLifetime = $this->getAdminPasswordLifetime();
            if ($passwordLifetime && $password && !$user->getForceNewPassword()) {
                $resource     = \Mage::getResourceSingleton('Magento\Pci\Model\Resource\Admin\User');
                $passwordHash = \Mage::helper('Magento\Core\Helper\Data')->getHash($password, false);
                $resource->trackPassword($user, $passwordHash, $passwordLifetime);
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')
                        ->getMessages()
                        ->deleteMessageByIdentifier('magento_pci_password_expired');
                \Mage::getSingleton('Magento\Backend\Model\Auth\Session')->unsPciAdminUserIsPasswordExpired();
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
        return 60 * (int)\Mage::getStoreConfig('admin/security/lockout_threshold');
    }

    /**
     * Get admin password lifetime
     *
     * @return int
     */
    public function getAdminPasswordLifetime()
    {
        return 86400 * (int)\Mage::getStoreConfig('admin/security/password_lifetime');
    }

    /**
     * Force admin to change password
     *
     * @param \Magento\Event\Observer $observer
     */
    public function forceAdminPasswordChange($observer)
    {
        if (!$this->isPasswordChangeForced()) {
            return;
        }
        $session = \Mage::getSingleton('Magento\Backend\Model\Auth\Session');
        if (!$session->isLoggedIn()) {
            return;
        }
        $actionList = array('adminhtml_system_account_index', 'adminhtml_system_account_save',
            'adminhtml_auth_logout');
        $controller = $observer->getEvent()->getControllerAction();
        if (\Mage::getSingleton('Magento\Backend\Model\Auth\Session')->getPciAdminUserIsPasswordExpired()) {
            if (!in_array($controller->getFullActionName(), $actionList)) {
                if ($this->_authorization->isAllowed('Magento_Adminhtml::myaccount')) {
                    $controller->getResponse()->setRedirect(\Mage::getSingleton('Magento\Backend\Model\Url')
                            ->getUrl('adminhtml/system_account/'));
                    $controller->setFlag('', \Magento\Core\Controller\Varien\Action::FLAG_NO_DISPATCH, true);
                    $controller->setFlag('', \Magento\Core\Controller\Varien\Action::FLAG_NO_POST_DISPATCH, true);
                } else {
                    /*
                     * if admin password is expired and access to 'My Account' page is denied
                     * than we need to do force logout with error message
                     */
                    \Mage::getSingleton('Magento\Backend\Model\Auth\Session')->unsetAll();
                    \Mage::getSingleton('Magento\Adminhtml\Model\Session')->unsetAll();
                    \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError(
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
        return (bool)(int)\Mage::getStoreConfig('admin/security/password_is_forced');
    }
}
