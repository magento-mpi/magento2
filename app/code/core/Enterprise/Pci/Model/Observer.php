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
 * @category   Enterprise
 * @package    Enterprise_Pci
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Pci observer model
 *
 * Implements hashes upgrading
 */
class Enterprise_Pci_Model_Observer
{
    /**
     * Admin locking and password hashing upgrade logic implementation
     *
     * @param Varien_Event_Observer $observer
     */
    public function adminAuthenticate($observer)
    {
        $password = $observer->getPassword();
        $user     = $observer->getUser();
        $resource = Mage::getResourceSingleton('enterprise_pci/admin_user');
        if ($observer->getResult()) {
            // check whether user is locked
            if ($lockExpires = $user->getLockExpires()) {
                $lockExpires = new Zend_Date($lockExpires, Varien_Date::DATETIME_INTERNAL_FORMAT);
                $lockExpires = $lockExpires->toValue();
                if ($lockExpires > time()) {
                    throw new Exception(Mage::helper('enterprise_pci')->__('This account is locked.'));
                }
            }
            $resource->unlock($user->getId());

            // upgrade admin password
            if (!Mage::helper('core')->getEncryptor()->validateHashByVersion($password, $user->getPassword())) {
                Mage::getModel('admin/user')->load($user->getId())
                    ->setNewPassword($password)->setForceNewPassword(true)
                    ->save();
            }
        }
        // update locking information
        else {
            if ($user->getId()) {
                $now = time();
                $lockThreshold = $this->getAdminLockThreshold();
                $maxFailures = (int)Mage::getStoreConfig('admin/security/lockout_failures');
                if ($maxFailures < 6) {
                    $maxFailures = 6;
                }
                $failuresNum = (int)$user->getFailuresNum();
                if ($firstFailureDate = $user->getFirstFailure()) {
                    $firstFailureDate = new Zend_Date($firstFailureDate, Varien_Date::DATETIME_INTERNAL_FORMAT);
                    $firstFailureDate = $firstFailureDate->toValue();
                }

                $updateFirstFailureDate = false;
                $updateLockExpires      = false;
                // set first failure date when this is first failure or last first failure expired
                if (0 === $failuresNum || !$firstFailureDate || (($now - $firstFailureDate) > $lockThreshold)) {
                    $updateFirstFailureDate = $now;
                }
                // otherwise lock user
                elseif ($failuresNum >= $maxFailures) {
                    $updateLockExpires = $now + $lockThreshold;
                }
                $resource->updateFaiure($user, $updateLockExpires, $updateFirstFailureDate);
            }
        }
    }

    /**
     * Upgrade API key hash when api user has logged in
     *
     * @param Varien_Event_Observer $observer
     */
    public function upgradeApiKey($observer)
    {
        $apiKey = $observer->getApiKey();
        $model  = $observer->getModel();
        if (!Mage::helper('core')->getEncryptor()->validateHashByVersion($apiKey, $model->getApiKey())) {
            Mage::getModel('acl/user')->load($model->getId())->setNewApiKey($apiKey)->save();
        }
    }

    /**
     * Upgrade customer password hash when customer has logged in
     *
     * @param Varien_Event_Observer $observer
     */
    public function upgradeCustomerPassword($observer)
    {
        $password = $observer->getPassword();
        $model    = $observer->getModel();
        if (!Mage::helper('core')->getEncryptor()->validateHashByVersion($password, $model->getPassword())) {
            $model->changePassword($password, false);
        }
    }

    /**
     * Harden admin password change.
     *
     * New password must be minimum 7 chars length and include alphanumeric characters
     * The password is compared to at least last 4 previous passwords to prevent setting them again
     *
     * @param Varien_Event_Observer $observer
     */
    public function checkAdminPasswordChange($observer)
    {
        /* @var $user Mage_Admin_Model_User */
        $user = $observer->getObject();
        $password = ($user->getNewPassword() ? $user->getNewPassword() : $user->getPassword());
        if ($password && !$user->getForceNewPassword()) {
            // validate password syntax
            $passwordLength = 7;
            if (Mage::helper('core/string')->strlen($password) < $passwordLength) {
                throw new Exception(Mage::helper('enterprise_pci')->__('Password must be at least of %d characters.', $passwordLength));
            }
            if (!preg_match('/[a-z]/iu', $password) || !preg_match('/[0-9]/u', $password)) {
                throw new Exception(Mage::helper('enterprise_pci')->__('Password must include both numeric and alphabetic characters.'));
            }

            if ($user->getId()) {
                // check whether password was used before
                $resource     = Mage::getResourceSingleton('enterprise_pci/admin_user');
                $passwordHash = Mage::helper('core')->getHash($password, false);
                foreach ($resource->getOldPasswords($user) as $oldPasswordHash) {
                    if ($passwordHash === $oldPasswordHash) {
                        throw new Exception(Mage::helper('enterprise_pci')->__('This password was already used, try another one.'));
                    }
                }
            }
        }
    }

    /**
     * Save new admin password
     *
     */
    public function trackAdminNewPassword($observer)
    {
        /* @var $user Mage_Admin_Model_User */
        $user = $observer->getObject();
        if ($user->getId()) {
            $password = $user->getNewPassword();
            if ($password && !$user->getForceNewPassword()) {
                $resource     = Mage::getResourceSingleton('enterprise_pci/admin_user');
                $passwordHash = Mage::helper('core')->getHash($password, false);
                $resource->trackPassword($user, $passwordHash,
                    86400 * (int)Mage::getStoreConfig('admin/security/password_lifetime')
                );
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
        $lockThreshold = (int)Mage::getStoreConfig('admin/security/lockout_threshold');
        if ($lockThreshold < 30) {
            $lockThreshold = 30;
        }
        $lockThreshold = $lockThreshold * 60;
        return $lockThreshold;
    }

    /**
     * Check whether admin password is expired and notify (BETA)
     *
     */
    public function actionPostDispatchAdmin()
    {
        if (!Mage::registry('enterprise_pci_password_notification')) {
            $session = Mage::getSingleton('admin/session');
            if ($session->isLoggedIn()) {
                $userId = $session->getUser()->getId();
                $latestPassword = Mage::getResourceSingleton('enterprise_pci/admin_user')->getLatestPassword($userId);
                if ($latestPassword) {
                    if (isset($latestPassword['expires']) && ((int)$latestPassword['expires'] < time())) {
                        Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('enterprise_pci')->__(
                            'Your password is expired. Please <a href="%s">change it</a>.',
                            Mage::getUrl('adminhtml/permissions_user/edit', array('user_id' => $userId))
                        ));
                        Mage::register('enterprise_pci_password_notification', true);
                    }
                }
            }
        }
    }
}
