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
     * Upgrade password hash if admin user has logged in
     *
     * @param Varien_Event_Observer $observer
     */
    public function upgradeAdminPassword($observer)
    {
        $password = $observer->getPassword();
        $model    = $observer->getModel();
        if ($model->getId()) {
            if (!Mage::helper('core')->getEncryptor()->validateHashByVersion($password, $model->getPassword())) {
                Mage::getModel('admin/user')->load($model->getId())->setNewPassword($password)->save();
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
}
