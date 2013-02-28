<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Launcher data helper
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Helper_Data extends Mage_Core_Helper_Data
{
    /**
     * Get current Store
     *
     * @return Mage_Core_Model_Store
     */
    public function getCurrentStoreView()
    {
        /** @var $storeManager Mage_Core_Model_StoreManager */
        $storeManager = Mage::getObjectManager()->get('Mage_Core_Model_StoreManager');
        $storeView = $storeManager->getDefaultStoreView();
        return $storeView;
    }
}
