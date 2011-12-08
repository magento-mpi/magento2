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
 * Staging group resource adapter
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Model_Resource_Adapter_Group extends Enterprise_Staging_Model_Resource_Adapter_Abstract
{
    /**
     * Create run
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @param Enterprise_Staging_Model_Staging_Event $event
     * @return Enterprise_Staging_Model_Resource_Adapter_Group
     */
    public function createRun(Enterprise_Staging_Model_Staging $staging, $event = null)
    {
        parent::createRun($staging, $event);

        $websites = $staging->getMapperInstance()->getWebsites();

        $createdStoreGroups = array();
        foreach ($websites as $website) {
            $stores = $website->getStores();

            foreach ($stores as $store) {
                $realStore = Mage::app()->getStore($store->getMasterStoreId());
                if (!$realStore) {
                    continue;
                }
                if (array_key_exists($realStore->getGroupId(), $createdStoreGroups)) {
                    $store->setGroupId($createdStoreGroups[$realStore->getGroupId()]);
                    continue;
                }

                $realStoreGroup = $realStore->getGroup();

                $rootCategory = (int) $realStoreGroup->getRootCategoryId();

                $stagingGroup = Mage::getModel('Mage_Core_Model_Store_Group');
                $stagingGroup->setData('website_id', $website->getStagingWebsiteId());
                $stagingGroup->setData('root_category_id', $rootCategory);
                $stagingGroup->setData('name', $realStoreGroup->getName());
                $stagingGroup->save();

                $masterWebsite = $website->getMasterWebsite();
                $stagingWebsite = $website->getStagingWebsite();
                if ($stagingWebsite && ($realStoreGroup->getId() == $masterWebsite->getDefaultGroupId()) ) {
                    $stagingWebsite->setData('default_group_id', $stagingGroup->getId());
                    $stagingWebsite->save();
                }

                $store->setGroupId($stagingGroup->getId());

                $createdStoreGroups[$realStore->getGroupId()] = $stagingGroup->getId();
            }
        }

        return $this;
    }
}
