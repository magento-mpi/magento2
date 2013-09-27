<?php
/**
 * Helper for EAV functionality in integration tests.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_TestFramework_Helper_Eav
{
    /**
     * Set increment id prefix in entity model.
     *
     * @param string $entityType
     * @param string $prefix
     */
    public static function setIncrementIdPrefix($entityType, $prefix)
    {
        $website = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Core_Model_StoreManagerInterface')->getWebsite();
        $storeId = $website->getDefaultStore()->getId();
        $entityTypeModel = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Eav_Model_Entity_Type')->loadByCode($entityType);
        /** @var Magento_Eav_Model_Entity_Store $entityStore */
        $entityStore = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Eav_Model_Entity_Store')->loadByEntityStore(
                $entityTypeModel->getId(),
                $storeId
            );
        $entityStore->setEntityTypeId($entityTypeModel->getId());
        $entityStore->setStoreId($storeId);
        $entityStore->setIncrementPrefix($prefix);
        $entityStore->save();
    }
}
