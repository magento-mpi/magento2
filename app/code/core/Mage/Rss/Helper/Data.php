<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Rss data helper
 *
 * @category   Mage
 * @package    Mage_Rss
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Rss_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Authenticate customer on frontend
     *
     */
    public function authFrontend()
    {
        $session = Mage::getSingleton('Mage_Rss_Model_Session');
        if ($session->isCustomerLoggedIn()) {
            return;
        }
        list($username, $password) = $this->authValidate();
        $customer = Mage::getModel('Mage_Customer_Model_Customer')->authenticate($username, $password);
        if ($customer && $customer->getId()) {
            Mage::getSingleton('Mage_Rss_Model_Session')->settCustomer($customer);
        } else {
            $this->authFailed();
        }
    }

    /**
     * Authenticates admin user and checks ACL. Returns user model upon successful authentication.
     * If user authentication fails, then shows error and exits php instantly.
     *
     * @param string $path
     * @return Mage_Admin_Model_User
     */
    public function authAdmin($path)
    {
        $session = Mage::getSingleton('Mage_Rss_Model_Session');
        if ($session->isAdminLoggedIn()) {
            return $session->getAdmin();
        }

        list($username, $password) = $this->authValidate();
        Mage::getSingleton('Mage_Adminhtml_Model_Url')->setNoSecret(true);
        $adminSession = Mage::getSingleton('Mage_Admin_Model_Session');
        $user = $adminSession->login($username, $password);
        if ($user && $user->getIsActive() == '1' && $adminSession->isAllowed($path)){
            $session->setAdmin($user);
            return $user;
        } else {
            // Error is shown and exit() is called
            Mage::helper('Mage_Core_Helper_Http')->authFailed();
        }
    }

    /**
     * Validate Authenticate
     *
     * @param array $headers
     * @return array
     */
    public function authValidate($headers=null)
    {
        $userPass = Mage::helper('Mage_Core_Helper_Http')->authValidate($headers);
        return $userPass;
    }

    /**
     * Send authenticate failed headers
     *
     */
    public function authFailed()
    {
        Mage::helper('Mage_Core_Helper_Http')->authFailed();
    }

    /**
     * Disable using of flat catalog and/or product model to prevent limiting results to single store. Probably won't
     * work inside a controller.
     *
     * @return null
     */
    public function disableFlat()
    {
        /* @var $flatHelper Mage_Catalog_Helper_Product_Flat */
        $flatHelper = Mage::helper('Mage_Catalog_Helper_Product_Flat');
        if ($flatHelper->isAvailable()) {
            /* @var $emulationModel Mage_Core_Model_App_Emulation */
            $emulationModel = Mage::getModel('Mage_Core_Model_App_Emulation');
            // Emulate admin environment to disable using flat model - otherwise we won't get global stats
            // for all stores
            $emulationModel->startEnvironmentEmulation(0, Mage_Core_Model_App_Area::AREA_ADMIN);
        }
    }
}
