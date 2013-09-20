<?php
/**
 * Helper for EAV functionality in integration tests.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestFramework\Helper;

class Eav
{
    /**
     * Set increment id prefix in entity model.
     *
     * @param string $entityType
     * @param string $prefix
     */
    public static function setIncrementIdPrefix($entityType, $prefix)
    {
        $website = \Mage::app()->getWebsite();
        $storeId = $website->getDefaultStore()->getId();
        $entityTypeModel = \Mage::getModel('Magento\Eav\Model\Entity\Type')->loadByCode($entityType);
        /** @var \Magento\Eav\Model\Entity\Store $entityStore */
        $entityStore = \Mage::getModel('Magento\Eav\Model\Entity\Store')->loadByEntityStore(
            $entityTypeModel->getId(),
            $storeId
        );
        $entityStore->setEntityTypeId($entityTypeModel->getId());
        $entityStore->setStoreId($storeId);
        $entityStore->setIncrementPrefix($prefix);
        $entityStore->save();
    }
}
