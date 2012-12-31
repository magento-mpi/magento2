<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
abstract class SalesOrder_AbstractTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Eav_Model_Entity_Store */
    protected static $_entityStore;

    /** @var array */
    protected static $_origData = array();

    /**
     * Restore increment ID prefix in entity model.
     */
    protected function _restoreIncrementIdPrefix()
    {
        $entityStoreModel = self::$_entityStore;
        if ($entityStoreModel instanceof Mage_Eav_Model_Entity_Store) {
            $entityStoreModel->loadByEntityStore($entityStoreModel->getEntityTypeId(), $entityStoreModel->getStoreId());
            $entityStoreModel->setIncrementPrefix(self::$_origData['increment_prefix'])
                ->setIncrementLastId(++self::$_origData['increment_last_id'])
                ->save();
        }
    }

    /**
     * Set increment id prefix in entity model.
     *
     * @param string $entityType
     * @param string $prefix
     */
    protected function _setIncrementIdPrefix($entityType, $prefix)
    {
        $website = Mage::app()->getWebsite();
        $storeId = $website->getDefaultStore()->getId();
        $entityTypeModel = Mage::getModel('Mage_Eav_Model_Entity_Type')->loadByCode($entityType);
        /** @var Mage_Eav_Model_Entity_Store $entityStore */
        $entityStore = Mage::getModel('Mage_Eav_Model_Entity_Store')->loadByEntityStore(
            $entityTypeModel->getId(),
            $storeId
        );
        $origPrefix = $entityStore->getIncrementPrefix() == null ? $storeId : $entityStore->getIncrementPrefix();
        self::$_origData['increment_prefix'] = $origPrefix;
        self::$_origData['increment_last_id'] = $entityStore->getIncrementLastId();
        $entityStore->setEntityTypeId($entityTypeModel->getId());
        $entityStore->setStoreId($storeId);
        $entityStore->setIncrementPrefix($prefix);
        $entityStore->save();
        self::$_entityStore = $entityStore;
    }
}
