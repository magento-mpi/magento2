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
    /**
     * Slave to master stores mapping
     *
     * @var array
     */
    protected $_slaveStoresToMasterStores               = array();
    
    /**
     * slave to master store group mapping
     *
     * @var array
     */
    protected $_slaveStoreGroupsToMasterStoreGroups     = array();
    
    /**
     * slave to master website mapping
     *
     * @var array
     */
    protected $_slaveWebsitesToMasterWebsites           = array();

    /**
     * staging to master store mapping
     *
     * @var array
     */
    protected $_stagingStoresToMasterStores             = array();
    
    /**
     * staging to master store group mapping
     *
     * @var array
     */
    protected $_stagingStoreGroupsToMasterStoreGroups   = array();
    
    /**
     * staging to master website mapping
     *
     * @var array
     */
    protected $_stagingWebsitesToMasterWebsites         = array();

    /**
     * master to slave store mapping
     *
     * @var array
     */
    protected $_masterStoresToSlaveStores               = array();
    
    /**
     * master to slave store group mapping
     *
     * @var array
     */
    protected $_masterStoreGroupsToSlaveStoreGroups     = array();
    
    /**
     * master to slave website mapping
     *
     * @var array
     */
    protected $_masterWebsitesToSlaveWebsites           = array();

    /**
     * master to staging store mapping
     *
     * @var array
     */
    protected $_masterStoresToStagingStores             = array();
    
    /**
     * master store groupe to staging sotre group mapping
     *
     * @var array
     */
    protected $_masterStoreGroupsToStagingStoreGroups   = array();
    
    /**
     * master website to staging website mapping
     *
     * @var array
     */
    protected $_masterWebsitesToStagingWebsites         = array();

    /**
     * array website tables
     *
     * @var array
     */
    protected $_websiteTable                            = array();
    
    /**
     *  array store group tables
     *
     * @var array
     */
    protected $_storeGroupTable                         = array();
    
    /**
     * array store group tables
     *
     * @var array
     */
    protected $_storeTable                              = array();

    /**
     * staging website tables
     *
     * @var array
     */
    protected $_stagingWebsiteTable                     = array();
    
    /**
     * staging store group tables
     *
     * @var array
     */
    protected $_stagingStoreGroupTable                  = array();
    
    /**
     * staging store tables
     *
     * @var array
     */
    protected $_stagingStoreTable                       = array();

    /**
     * user items list
     *
     * @var array
     */
    protected $_usedItems                               = array();

    /**
     * used create items list
     *
     * @var array
     */
    protected $_usedCreateItems                         = array();

    /**
     * constructor
     *
     */
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


    /**
     * set mapper data
     *
     * @param array $mapData
     */
    public function setCreateMapData($mapData)
    {
        $websitesMap    = !empty($mapData['websites']) ? $mapData['websites'] : array();
        $storesMap      = !empty($mapData['stores']) ? $mapData['stores'] : array();

        $items          = $this->getStaging()->getDatasetItemsCollection();

        foreach ($websitesMap as $id => $websiteMap) {
            if (!is_array($websiteMap)) {
                continue;
            }
            $websiteItems = !empty($websiteMap['dataset_items']) ? $websiteMap['dataset_items'] : array();

            foreach ($websiteItems as $idx => $websiteItemId) {
                $datasetItem = $items->getItemById($websiteItemId);
                if ($datasetItem) {
                    $websiteItems[$idx] = $datasetItem->getData();
                }
            }
            $this->_usedCreateItems[$id]['items']  = $websiteItems;

            $storesMap   = !empty($storesMap[$id]) ? $storesMap[$id] : array();
            foreach ($storesMap as $storeMap) {
                $storeId = !empty($storeMap['master_store_id']) ? $storeMap['master_store_id'] : false;
                if ($storeId) {
                    $storeItems = !empty($storeMap['dataset_items']) ? $storeMap['dataset_items'] : array();
                    foreach ($storeItems as $idx => $storeItemId) {
                        $datasetItem = $items->getItemById($storeItemId);
                        if ($datasetItem) {
                            $storeItems[$idx] = $datasetItem->getData();
                        }
                    }
                    $this->_usedCreateItems[$id]['stores'][$storeId] = $storeItems;
                }
            }
        }
    }

    /**
     * get Store used items list
     *
     * @param int $websiteId
     * @param int $storeId
     * @return array
     */
    public function getStoreUsedCreateItems($websiteId, $storeId = null)
    {
        if (!isset($this->_usedCreateItems[$websiteId]['stores'])) {
            return array();
        }

        if (is_null($storeId)) {
            return $this->_usedCreateItems[$websiteId]['stores'];
        } else {
            if (isset($this->_usedCreateItems[$websiteId]['stores'][$storeId])) {
                return $this->_usedCreateItems[$websiteId]['stores'][$storeId];
            } else {
                return array();
            }
        }
    }

    /**
     * get website user list
     *
     * @param int $websiteId
     * @return array
     */
    public function getWebsiteUsedCreateItems($websiteId = null)
    {
        if (is_null($websiteId)) {
            return $this->_usedCreateItems;
        } else {
            if (isset($this->_usedCreateItems[$websiteId]['items'])) {
                return $this->_usedCreateItems[$websiteId]['items'];
            } else {
                return array();
            }
        }
    }

    /**
     * set Mapper data
     *
     * @param array $mapData
     */
    public function setMapData($mapData)
    {
        $websitesMap = !empty($mapData['websites']) ? $mapData['websites'] : array();
        $storesMap = !empty($mapData['stores']) ? $mapData['stores'] : array();

        $_usedItems = !empty($mapData['items']) ? $mapData['items'] : array();

        foreach ($_usedItems as $code => $item) {
            if (!empty($item['dataset_item_id'])) {
                $this->_usedItems[$code] = $item;
            } else {
                unset($this->_usedItems[$code]);
            }
        }

        if (!empty($websitesMap)) {
            $fromWebsitesData   = !empty($websitesMap['from'])   ? $websitesMap['from']   : array();
            $toWebsitesData     = !empty($websitesMap['to'])     ? $websitesMap['to']     : array();

            foreach ($fromWebsitesData as $idx => $fromWebsite) {
                if (empty($fromWebsite)) {
                    continue;
                }

                foreach ($toWebsitesData as $_toIdx => $toWebsite) {
                    if (empty($toWebsite)) {
                        continue;
                    }
                    $this->_slaveWebsitesToMasterWebsites[$fromWebsite]['master_website'][$toWebsite] = $fromWebsite;
                    $this->_masterWebsitesToSlaveWebsites[$toWebsite]['slave_website'][$fromWebsite] = $toWebsite;

                    $storesKey = $fromWebsite . '-' . $toWebsite;
                    $storesData = !empty($storesMap[$storesKey]) ? $storesMap[$storesKey] : array();

                    if (!empty($storesData)) {
                        $storesFromData = !empty($storesData['from']) ? $storesData['from'] : array();
                        $storesToData = !empty($storesData['to']) ? $storesData['to'] : array();
                        foreach ($storesFromData as $sidx => $fromStore) {
                            if (!empty($storesToData[$sidx])) {
                                $toStore = $storesToData[$sidx];
                                //var_dump($fromWebsite.' - '.$toWebsite.' - '.$fromStore.' - '.$toStore); echo '<br />';
                                $this->_slaveWebsitesToMasterWebsites[$fromWebsite]['stores'][$toWebsite][$fromStore] = $toStore;
                                $this->_masterWebsitesToSlaveWebsites[$toWebsite]['stores'][$fromWebsite][$toStore] = $fromStore;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * setialize main map data
     *
     * @param array $attributes
     * @param string $valueSeparator
     * @param string $fieldSeparator
     * @param string $quote
     * @return string
     */
    public function serialize($attributes = array(), $valueSeparator='=', $fieldSeparator=' ', $quote='"')
    {
        $resArray["_usedItems"] = $this->_usedItems;
        $resArray["_slaveWebsitesToMasterWebsites"] = $this->_slaveWebsitesToMasterWebsites;
        $resArray["_masterWebsitesToSlaveWebsites"] = $this->_masterWebsitesToSlaveWebsites;
        return serialize($resArray);
    }

    /**
     * unserialize map array and init mapper
     *
     * @param string $serializedData
     */
    public function unserialize($serializedData)
    {
        $unserializedArray = unserialize($serializedData);
        if ($unserializedArray) {

            if ( !empty( $unserializedArray["_usedItems"] )) {
                $this->_usedItems = $unserializedArray["_usedItems"];
            }

            if ( !empty( $unserializedArray["_masterWebsitesToSlaveWebsites"] )) {
                $this->_masterWebsitesToSlaveWebsites = $unserializedArray["_masterWebsitesToSlaveWebsites"];
            }

            if ( !empty( $unserializedArray["_slaveWebsitesToMasterWebsites"] )) {
                $this->_slaveWebsitesToMasterWebsites = $unserializedArray["_slaveWebsitesToMasterWebsites"];
            }

        }
    }

    /**
     * return user website array
     *
     * @param int $id
     * @return array
     */
    public function getUsedWebsites($id)
    {
        return isset($this->_slaveWebsitesToMasterWebsites[$id]) ? $this->_slaveWebsitesToMasterWebsites[$id] : array();
    }

    /**
     * get all used websites
     *
     * @return array
     */
    public function getAllUsedWebsites()
    {
        return isset($this->_slaveWebsitesToMasterWebsites) ? $this->_slaveWebsitesToMasterWebsites : array();
    }

    /**
     * get all slave used websites
     *
     * @param int $masterWebsiteId
     * @return array
     */
    public function getSlaveToMasterWebsiteIds($masterWebsiteId)
    {
        return isset($this->_masterWebsitesToSlaveWebsites[$masterWebsiteId]['slave_website']) ? $this->_masterWebsitesToSlaveWebsites[$masterWebsiteId]['slave_website'] : array();
    }

    /**
     * get all slave used stores
     *
     * @param int $slaveWebsiteId
     * @return array
     */
    public function getSlaveToMasterStoreIds($slaveWebsiteId)
    {
        return isset($this->_slaveWebsitesToMasterWebsites[$slaveWebsiteId]['stores']) ? $this->_slaveWebsitesToMasterWebsites[$slaveWebsiteId]['stores'] : array();
    }

    /**
     * return all used in mapping items
     *
     * @return array
     */
    public function getUsedItems()
    {
        return $this->_usedItems;
    }

    /**
     * return slave stores by master id
     *
     * @param int $website
     * @param int $storeId
     * @return int
     */
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