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
 * @category    Enterprise
 * @package     Enterprise_Search
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */
class Enterprise_Search_Model_Resource_Engine
{
    /**
     * Store search engine adapter model instance
     *
     * @var object
     */
    protected $_adapter = null;

    /**
     * Set search engine adapter
     *
     */
    public function __construct()
    {
        $this->_adapter = $this->_getAdapterModel('solr');
    }

    /**
     * Retrieve found document ids search index sorted by relevance
     *
     * @param string $query
     * @param array $params see description in appropriate search adapter
     * @param string $entityType 'product'|'cms'
     * @return array
     */
    public function getIdsByQuery($query, $params = array(), $entityType = 'product')
    {
        return $this->_adapter->getIdsByQuery($query, $params);
    }

    /**
     * Retrieve search suggestions
     *
     * @param string $query
     * @param array $params see description in appropriate search adapter
     * @return array
     */
    public function getSuggestionsByQuery($query, $params=array(), $limit=false, $withResultsCounts=false)
    {
        return $this->_adapter->getSuggestionsByQuery($query, $params, $limit, $withResultsCounts);
    }

    /**
     * Add entity data to search index
     *
     * @param int $entityId
     * @param int $storeId
     * @param array $index
     * @param string $entityType 'product'|'cms'
     * @return Enterprise_Search_Model_Resource_Engine
     */
    public function saveEntityIndex($entityId, $storeId, $index, $entityType = 'product')
    {
        $store             = Mage::app()->getStore($storeId);
        $localeCode        = $store->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE);
        $index['store_id'] = $storeId;
        $docs = $this->_adapter->prepareDocs(array($entityId => $index), $localeCode);
        $this->_adapter->addDocs($docs);
        return $this;
    }

    /**
     * Multi add entities data to search index
     *
     * @param int $storeId
     * @param array $entityIndexes
     * @param string $entityType 'product'|'cms'
     * @return Enterprise_Search_Model_Resource_Engine
     */
    public function saveEntityIndexes($storeId, $entityIndexes, $entityType = 'product')
    {
        $store             = Mage::app()->getStore($storeId);
        $localeCode        = $store->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE);
        foreach ($entityIndexes as $entityId => $indexData) {
            $entityIndexes[$entityId]['store_id'] = $storeId;
        }
        $docs = $this->_adapter->prepareDocs($entityIndexes, $localeCode);
        $this->_adapter->addDocs($docs);
        return $this;
    }

    /**
     * Remove entity data from search index
     *
     * @param int $storeId
     * @param int $entityId
     * @param string $entityType 'product'|'cms'
     * @return Enterprise_Search_Model_Resource_Engine
     */
    public function cleanIndex($storeId = null, $entityId = null, $entityType = 'product')
    {
        if (is_null($storeId) && is_null($entityId)) {
            $this->_adapter->deleteDocs(array(), 'all');
        }
        elseif (is_null($storeId) && !is_null($entityId)) {
            $this->_adapter->deleteDocs($entityId);
        }
        elseif (!is_null($storeId) && is_null($entityId)) {
            $this->_adapter->deleteDocs(array(), array('store_id:' . $storeId));
        }
        elseif (!is_null($storeId) && !is_null($entityId)) {
            $idsQuery = array();
            if (!is_array($entityId)) {
                $entityId = array($entityId);
            }
            foreach ($entityId as $id) {
                $idsQuery[] = $this->_adapter->getUniqueKey() . ':' . $id;
            }
            $this->_adapter->deleteDocs(array(), array('store_id:' . $storeId . ' AND (' . implode(' OR ', $idsQuery) . ')'));
        }
        return $this;
    }

    /**
     * Retrieve last query number of found results
     *
     * @return int
     */
    public function getLastNumFound()
    {
        return $this->_adapter->getLastNumFound();
    }

    /**
     * Retrieve search result data collection
     *
     * @return Enterprise_Search_Model_Resource_Collection
     */
    public function getResultCollection()
    {
        return Mage::getResourceModel('enterprise_search/collection')->setEngine($this);
    }

    /**
     * Prepare index array
     *
     * @param array $index
     * @param string $separator
     * @return array
     */
    public function prepareEntityIndex($index, $separator = null)
    {
        return $index;
    }

    /**
     * Define if Layered Navigation is allowed
     *
     * @return bool
     */
    public function isLeyeredNavigationAllowed()
    {
        return false;
    }

    /**
     * Retrieve search engine adapter model by adapter name
     * Now suppoting only Solr search engine adapter
     *
     * @param string $adapterName
     * @return object
     */
    protected function _getAdapterModel($adapterName)
    {
        $model = '';
        switch ($adapterName) {
            case 'solr':
            default:
                if (extension_loaded('solr')) {
                    $model = 'enterprise_search/adapter_phpExtension';
                }
                else {
                    $model = 'enterprise_search/adapter_httpStream';
                }
                break;
        }
        return Mage::getSingleton($model);
    }

    /**
     * Define if selected adapter is avaliable
     *
     * @return bool
     */
    public function test()
    {
        return $this->_adapter->ping();
    }
}
