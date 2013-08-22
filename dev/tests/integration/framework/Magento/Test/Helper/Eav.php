<?php
/**
 * Helper for EAV functionality in integration tests.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Test_Helper_Eav
{
    /**
     * Set increment id prefix in entity model.
     *
     * @param string $entityType
     * @param string $prefix
     */
    public static function setIncrementIdPrefix($entityType, $prefix)
    {
        $website = Mage::app()->getWebsite();
        $storeId = $website->getDefaultStore()->getId();
        $entityTypeModel = Mage::getModel('Magento_Eav_Model_Entity_Type')->loadByCode($entityType);
        /** @var Magento_Eav_Model_Entity_Store $entityStore */
        $entityStore = Mage::getModel('Magento_Eav_Model_Entity_Store')->loadByEntityStore(
            $entityTypeModel->getId(),
            $storeId
        );
        $entityStore->setEntityTypeId($entityTypeModel->getId());
        $entityStore->setStoreId($storeId);
        $entityStore->setIncrementPrefix($prefix);
        $entityStore->save();
    }
}
