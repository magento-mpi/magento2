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
     * Check if admin is logged in and authorized to access resource by specified ACL path
     *
     * If not authenticated, will try to do it using provided credentials
     *
     * @param Mage_Backend_Model_Auth_Session $session
     * @param string $login
     * @param string $password
     * @param string $aclPath
     * @return bool
     */
    public function isAdminAuthorized($session, $login, $password, $aclPath)
    {
        if ($session->isLoggedIn()) {
            return true;
        }
        if (!$login || !$password) {
            return false;
        }
        /** @var $auth Mage_Backend_Model_Auth */
        $auth = Mage::getModel('Mage_Backend_Model_Auth');
        $auth->setAuthStorage($session);
        try {
            $auth->login($login, $password);
        } catch (Mage_Backend_Model_Auth_Exception $e) {
            return false;
        }
        $user = $session->getUser();
        if ($user && $user->getIsActive() == '1' && $session->isAllowed($aclPath)){
            $session->setAdmin($user);
            return true;
        }
        return false;
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
