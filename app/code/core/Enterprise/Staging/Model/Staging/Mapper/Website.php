<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_Staging_Model_Staging_Mapper_Website extends Enterprise_Staging_Model_Staging_Mapper_Abstract
{
    protected $_createMapData   = array();

    protected $_mergeMapData    = array();

    protected $_rollbackMapData = array();

    /**
     * set create map data
     *
     * @param array $mapData
     */
    public function setCreateMapData($mapData)
    {
        $this->_createMapData = $mapData;

        $websites = !empty($mapData['websites']) ? $mapData['websites'] : array();
        if (!empty($websites)) {
            foreach ($websites as $masterWebsiteId => $websiteMap) {
                $websites[$masterWebsiteId] = $this->_addCreateWebsiteMap($websiteMap);
            }
            $this->setData('websites', $websites);
        }

        $stagingItems = !empty($mapData['staging_items']) ? $mapData['staging_items'] : array();
        $this->addStagingItemsMap($stagingItems);

        return $this;
    }

    protected function _addCreateWebsiteMap($websiteMap)
    {
        $stores = !empty($websiteMap['stores']) ? $websiteMap['stores'] : array();

        $websiteMap = new Varien_Object($websiteMap);

        foreach ($stores as $masterStoreId => $storeMap) {
            if (isset($storeMap['use'])) {
                $stores[$masterStoreId] = $this->getStoreWrapper($storeMap);
            }
        }
        $websiteMap->setData('stores', $stores);

        return $websiteMap;
    }

    public function getStoreWrapper($data)
    {
        if (!($data instanceof Varien_Object)) {
            return new Varien_Object($data);
        } else {
            return $data;
        }
    }

    public function getWebsite($websiteId)
    {
        $websites = $this->getData('websites');
        return isset($websites[$websiteId]) ? $websites[$websiteId] : false;
    }

    public function getCreateMapData()
    {
        return $this->_createMapData;
    }

    public function setMergeMapData($mapData)
    {
        $this->_mergeMapData = $mapData;

        $websitesMap = !empty($mapData['websites']) ? $mapData['websites'] : array();
        $this->addWebsitesMap($websitesMap);

        $storesMap = !empty($mapData['stores']) ? $mapData['stores'] : array();
        $this->addStoresMap($storesMap);

        $stagingItems = !empty($mapData['staging_items']) ? $mapData['staging_items'] : array();

        $this->addStagingItemsMap($stagingItems);

        return $this;
    }

    public function addWebsitesMap(array $websitesMap)
    {
        if (!empty($websitesMap)) {
            $_websitesMap = array();
            $fromWebsitesData   = !empty($websitesMap['from'])   ? $websitesMap['from']   : array();
            $toWebsitesData     = !empty($websitesMap['to'])     ? $websitesMap['to']     : array();
            foreach ($fromWebsitesData as $_idx => $stagingWebsiteId) {
                if (!empty($stagingWebsiteId)) {
                    $_websitesMap[$stagingWebsiteId][] = $toWebsitesData[$_idx];
                }
            }
            $this->setData('websites', $_websitesMap);
        }

        return $this;
    }

    public function addStoresMap(array $storesMap)
    {
        if (!empty($storesMap)) {
            foreach ($storesMap as &$storeMap) {
                $fromStoresData   = !empty($storeMap['from']) ? $storeMap['from'] : array();
                $toStoresData     = !empty($storeMap['to'])   ? $storeMap['to']   : array();
                foreach ($fromStoresData as $_idx => $stagingStoreId) {
                    if (!empty($stagingStoreId) && !empty($toStoresData[$_idx])) {
                        $_storesMap[$stagingStoreId][] = $toStoresData[$_idx];
                    }
                }
            }
            $this->setData('stores', $_storesMap);
        }

        return $this;
    }

    public function addStagingItemsMap(array $stagingItems)
    {
        if (!empty($stagingItems)) {
            foreach ($stagingItems as $stagingItemCode => $stagingItemInfo) {
                $stagingItem = Enterprise_Staging_Model_Staging_Config::getStagingItem($stagingItemCode);
                if ($stagingItem) {
                    $stagingItems[$stagingItemCode] = $stagingItem;
                } else {
                    unset($stagingItems[$stagingItemCode]);
                }
            }
            $this->setData('staging_items', $stagingItems);
        }

        return $this;
    }

    public function getMergeMapData()
    {
        return $this->_mergeMapData;
    }

    /**
     * set rollback map data
     *
     * @param array $mapData
     */
    public function setRollbackMapData($mapData)
    {
        $this->_rollbackMapData = $mapData;

        $websitesMap = !empty($mapData['websites']) ? $mapData['websites'] : array();
        $this->addWebsitesMap($websitesMap);

        $storesMap = !empty($mapData['stores']) ? $mapData['stores'] : array();
        $this->addStoresMap($storesMap);

        $stagingItems = !empty($mapData['staging_items']) ? $mapData['staging_items'] : array();

        $this->addStagingItemsMap($stagingItems);

        return $this;
    }

    /**
     * setialize main map data
     *
     * @return string
     */
    public function serialize($attributes = array(), $valueSeparator='=', $fieldSeparator=' ', $quote='"')
    {
        $resArray = array();

        $resArray["_createMapData"]     = $this->_createMapData;
        $resArray["_mergeMapData"]      = $this->_mergeMapData;
        $resArray["_rollbackMapData"]   = $this->_rollbackMapData;

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
            if ( !empty($unserializedArray["_createMapData"])) {
                $this->setCreateMapData($unserializedArray["_createMapData"]);
            }
            if ( !empty($unserializedArray["_mergeMapData"])) {
                $this->setMergeMapData($unserializedArray["_mergeMapData"]);
            }
            if ( !empty($unserializedArray["_rollbackMapData"])) {
                $this->setRollbackMapData($unserializedArray["_rollbackMapData"]);
            }
        }

        return $this;
    }
}