<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Enterprise_Staging_Model_Staging_Mapper_Website extends Enterprise_Staging_Model_Staging_Mapper_Abstract
{
    protected $_slaveStoresToMasterStores               = array();
    protected $_slaveStoreGroupsToMasterStoreGroups     = array();
    protected $_slaveWebsitesToMasterWebsites           = array();

    protected $_stagingStoresToMasterStores             = array();
    protected $_stagingStoreGroupsToMasterStoreGroups   = array();
    protected $_stagingWebsitesToMasterWebsites         = array();

    protected $_masterStoresToSlaveStores               = array();
    protected $_masterStoreGroupsToSlaveStoreGroups     = array();
    protected $_masterWebsitesToSlaveWebsites           = array();

    protected $_masterStoresToStagingStores             = array();
    protected $_masterStoreGroupsToStagingStoreGroups   = array();
    protected $_masterWebsitesToStagingWebsites         = array();

    protected $_websiteTable                            = array();
    protected $_storeGroupTable                         = array();
    protected $_storeTable                              = array();

    protected $_stagingWebsiteTable                     = array();
    protected $_stagingStoreGroupTable                  = array();
    protected $_stagingStoreTable                       = array();

    protected $_usedItems                               = array();

    public function __construct()
    {
        parent::__construct();

        $this->_websiteTable                = $this->getTable('core/website');
        $this->_storeGroupTable             = $this->getTable('core/store_group');
        $this->_storeTable                  = $this->getTable('core/store');

        $this->_stagingWebsiteTable         = $this->getTable('enterprise_staging/staging_website');
        $this->_stagingStoreGroupTable      = $this->getTable('enterprise_staging/staging_store_group');
        $this->_stagingStoreTable           = $this->getTable('enterprise_staging/staging_store');
    }

    public function setMapData($mapData)
    {
        $websitesMap = !empty($mapData['websites']) ? $mapData['websites'] : array();
        $storesMap = !empty($mapData['stores']) ? $mapData['stores'] : array();
        $this->_usedItems = !empty($mapData['items']) ? $mapData['items'] : array();

        if (!empty($websitesMap)) {
            $fromWebsitesData   = !empty($websitesMap['from'])   ? $websitesMap['from']   : array();
            $toWebsitesData     = !empty($websitesMap['to'])     ? $websitesMap['to']     : array();

            foreach ($fromWebsitesData as $idx => $fromWebsite) {
                if (empty($fromWebsite)) {
                    continue;
                }

                $toWebsite = $toWebsitesData[$idx];
                $this->_slaveWebsitesToMasterWebsites[$fromWebsite]['master_website'] = $toWebsite;
                $this->_masterWebsitesToSlaveWebsites[$toWebsite]['slave_website'] = $fromWebsite;

                $storesKey = $fromWebsite . '-' . $toWebsite;
                $storesData = !empty($storesMap[$storesKey]) ? $storesMap[$storesKey] : array();
                if (!empty($storesData)) {
                    $storesFromData = !empty($storesData['from']) ? $storesData['from'] : array();
                    $storesToData = !empty($storesData['to']) ? $storesData['to'] : array();
                    foreach ($storesFromData as $sidx => $fromStore) {
                        if (!empty($storesToData[$sidx])) {
                            $toStore = $storesToData[$sidx];
                            $this->_slaveWebsitesToMasterWebsites[$fromWebsite]['stores'][$fromStore] = $toStore;
                            $this->_masterWebsitesToSlaveWebsites[$toWebsite]['stores'][$toStore] = $fromStore;
                        }
                    }
                }
            }
        }
    }

    public function getSlaveToMasterWebsiteIds($masterWebsiteId)
    {
        return isset($this->_masterWebsitesToSlaveWebsites[$masterWebsiteId]['slave_website']) ? $this->_masterWebsitesToSlaveWebsites[$masterWebsiteId]['slave_website'] : array();
    }

    public function getSlaveToMasterStoreIds($masterWebsiteId)
    {
        return isset($this->_masterWebsitesToSlaveWebsites[$masterWebsiteId]['stores']) ? $this->_masterWebsitesToSlaveWebsites[$masterWebsiteId]['stores'] : array();
    }

    public function getUsedItems()
    {
        return $this->_usedItems;
    }

    public function getSlaveStoreIdByMasterStoreId($website, $storeId)
    {
        $this->map($website);

        $websiteId = $website->getId();
        $select = $this->_read->select()
           ->from($this->_stagingStoreTable, 'staging_store_id')
           ->where('staging_website_id = ?', $websiteId)
           ->where('master_store_id = ?', $storeId);

       return $this->_read->fetchOne($select);
    }
}