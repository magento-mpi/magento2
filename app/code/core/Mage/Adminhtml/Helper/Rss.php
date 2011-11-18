<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Default rss helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Helper_Rss extends Mage_Core_Helper_Abstract
{
    public function authAdmin($path)
    {
        $session = Mage::getSingleton('Mage_Rss_Model_Session');
        if ($session->isAdminLoggedIn()) {
            return;
        }
        list($username, $password) = Mage::helper('Mage_Core_Helper_Http')->authValidate();
        $adminSession = Mage::getModel('Mage_Admin_Model_Session');
        $user = $adminSession->login($username, $password);
        //$user = Mage::getModel('Mage_Admin_Model_User')->login($username, $password);
        if($user && $user->getId() && $user->getIsActive() == '1' && $adminSession->isAllowed($path)){
            $session->setAdmin($user);
        } else {
            Mage::helper('Mage_Core_Helper_Http')->authFailed();
        }
    }
}
