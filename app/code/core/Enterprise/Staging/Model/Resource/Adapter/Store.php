<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Enter description here ...
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Model_Resource_Adapter_Store extends Enterprise_Staging_Model_Resource_Adapter_Abstract
{
    /**
     * Create staging store views
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @param Enterprise_Staging_Model_Staging_Event $event
     * @return Enterprise_Staging_Model_Resource_Adapter_Store
     */
    public function createRun(Enterprise_Staging_Model_Staging $staging, $event = null)
    {
        parent::createRun($staging, $event);

        $websites       = $staging->getMapperInstance()->getWebsites();
        $masterWebsite  = $staging->getMasterWebsite();

        $defaultStoreId = null;
        if ($masterWebsite) {
            $masterDefaultGroup = $masterWebsite->getDefaultGroup();
            if ($masterDefaultGroup) {
                $defaultStoreId = $masterDefaultGroup->getDefaultStoreId();
            }
        }

        foreach ($websites as $website) {
            $stores = $website->getStores();
            foreach ($stores as $masterStoreId => $store) {
                $stagingStore = Mage::getModel('Mage_Core_Model_Store');
                $stagingStore->setData('is_active', 1);
                $stagingStore->setData('is_staging', 1);
                $stagingStore->setData('code', $store->getCode());
                $stagingStore->setData('name', $store->getName());

                $stagingWebsite = $website->getStagingWebsite();
                if ($stagingWebsite) {
                    $stagingStore->setData('website_id', $website->getStagingWebsiteId());
                    $stagingStore->setData('group_id', $stagingWebsite->getDefaultGroupId());
                }

                if ($store->getGroupId()) {
                    $stagingStore->setData('group_id', $store->getGroupId());
                }

                if (!$stagingStore->getId()) {
                    $value = Mage::getModel('Mage_Core_Model_Date')->gmtDate();
                    $stagingStore->setCreatedAt($value);
                } else {
                    $value = Mage::getModel('Mage_Core_Model_Date')->gmtDate();
                    $stagingStore->setUpdatedAt($value);
                }

                $stagingStore->save();

                if ($stagingWebsite) {
                    $defaultGroup = $stagingWebsite->getDefaultGroup();
                    if ($defaultGroup) {
                        if (!$defaultGroup->getDefaultStoreId()
                            && (is_null($defaultStoreId) ||
                            ($stagingStore->getId() == $defaultStoreId))) {
                                $defaultGroup->setDefaultStoreId($stagingStore->getId());
                                $defaultGroup->save();
                        }
                    }
                }

                $store->setStagingStore($stagingStore);
                $store->setStagingStoreId($stagingStore->getId());
            }
        }

        return $this;
    }
}
