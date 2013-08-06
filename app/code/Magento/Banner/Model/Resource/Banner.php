<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Banner resource module
 *
 * @category    Magento
 * @package     Magento_Banner
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Banner_Model_Resource_Banner extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Sales rule table name
     *
     * @var string
     */
    protected $_salesRuleTable;

    /**
     * Catalog rule table name
     *
     * @var string
     */
    protected $_catalogRuleTable;

    /**
     * Contents table name
     *
     * @var string
     */
    protected $_contentsTable;

    /**
     * Define if joining related sales rule to banner is already preformed
     *
     * @var bool
     */
    protected $_isSalesRuleJoined       = false;

    /**
     * Define if joining related catalog rule to banner is already preformed
     *
     * @var bool
     */
    protected $_isCatalogRuleJoined     = false;

    /**
     * Whether to filter banners by specified types
     *
     * @var array
     */
    protected $_bannerTypesFilter                = array();

    /**
     * @var Magento_Core_Model_Event_Manager
     */
    private $_eventManager;

    /**
     * @var Magento_Banner_Model_Config
     */
    private $_bannerConfig;

    /**
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Banner_Model_Config $bannerConfig
     */
    public function __construct(
        Magento_Core_Model_Resource $resource,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Banner_Model_Config $bannerConfig
    ) {
        parent::__construct($resource);
        $this->_eventManager = $eventManager;
        $this->_bannerConfig = $bannerConfig;
    }

    /**
     * Initialize banner resource model
     *
     */
    protected function _construct()
    {
        $this->_init('magento_banner', 'banner_id');
        $this->_salesRuleTable       = $this->getTable('magento_banner_salesrule');
        $this->_catalogRuleTable     = $this->getTable('magento_banner_catalogrule');
        $this->_contentsTable        = $this->getTable('magento_banner_content');
    }

    /**
     * Set filter by specified types
     *
     * @param string|array $types
     * @return Magento_Banner_Model_Resource_Banner
     */
    public function filterByTypes($types = array())
    {
        $this->_bannerTypesFilter = $this->_bannerConfig->explodeTypes($types);
        return $this;
    }

    /**
     * Save banner contents for different store views
     *
     * @param int $bannerId
     * @param array $contents
     * @param array $notuse
     * @return Magento_Banner_Model_Resource_Banner
     */
    public function saveStoreContents($bannerId, $contents, $notuse = array())
    {
        $deleteByStores = array();
        if (!is_array($notuse)) {
            $notuse = array();
        }
        $adapter = $this->_getWriteAdapter();

        foreach ($contents as $storeId => $content) {
            if (!empty($content)) {
                $adapter->insertOnDuplicate(
                    $this->_contentsTable,
                    array('banner_id' => $bannerId, 'store_id' => $storeId, 'banner_content' => $content),
                    array('banner_content')
                );
            } else {
                $deleteByStores[] = $storeId;
            }
        }
        if (!empty($deleteByStores) || !empty($notuse)) {
            $condition = array(
                'banner_id = ?'   => $bannerId,
                'store_id IN (?)' => array_merge($deleteByStores, array_keys($notuse)),
            );
            $adapter->delete($this->_contentsTable, $condition);
        }
        return $this;
    }

    /**
     * Delete unchecked catalog rules
     *
     * @param int $bannerId
     * @param array $rules
     * @return Magento_Banner_Model_Resource_Banner
     */
    public function saveCatalogRules($bannerId, $rules)
    {
        $adapter = $this->_getWriteAdapter();
        if (empty($rules)) {
            $rules = array(0);
        } else {
            foreach ($rules as $ruleId) {
                $adapter->insertOnDuplicate(
                    $this->_catalogRuleTable,
                    array('banner_id' => $bannerId, 'rule_id' => $ruleId),
                    array('rule_id')
                );
            }
        }
        $condition = array(
            'banner_id=?'        => $bannerId,
            'rule_id NOT IN (?)' => $rules
        );
        $adapter->delete($this->_catalogRuleTable, $condition);
        return $this;
    }

    /**
     * Delete unchecked sale rules
     *
     * @param int $bannerId
     * @param array $rules
     * @return Magento_Banner_Model_Resource_Banner
     */
    public function saveSalesRules($bannerId, $rules)
    {
        $adapter = $this->_getWriteAdapter();
        if (empty($rules)) {
            $rules = array(0);
        } else {
            foreach ($rules as $ruleId) {
                $adapter->insertOnDuplicate(
                    $this->_salesRuleTable,
                    array('banner_id' => $bannerId, 'rule_id' => $ruleId),
                    array('rule_id')
                );
            }
        }
        $adapter->delete($this->_salesRuleTable,
            array('banner_id=?' => $bannerId, 'rule_id NOT IN (?)' => $rules)
        );
        return $this;
    }

    /**
     * Get all existing banner contents
     *
     * @param int $bannerId
     * @return array
     */
    public function getStoreContents($bannerId)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->_contentsTable, array('store_id', 'banner_content'))
            ->where('banner_id=?', $bannerId);
        return $adapter->fetchPairs($select);
    }

    /**
     * Get banner content by specific store id
     *
     * @param int $bannerId
     * @param int $storeId
     * @return string
     */
    public function getStoreContent($bannerId, $storeId)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from(array('main_table' => $this->_contentsTable), 'banner_content')
            ->where('main_table.banner_id = ?', $bannerId)
            ->where('main_table.store_id IN (?)', array($storeId, 0))
            ->order('main_table.store_id DESC');

        if ($this->_bannerTypesFilter) {
            $select->joinInner(
                array('banner' => $this->getTable('magento_banner')),
                'main_table.banner_id = banner.banner_id'
            );
            $filter = array();
            foreach ($this->_bannerTypesFilter as $type) {
                $filter[] = $adapter->prepareSqlCondition('banner.types', array('finset' => $type));
            }
            $select->where(implode(' OR ', $filter));
        }

        $this->_eventManager->dispatch('magento_banner_resource_banner_content_select_init', array(
            'select' => $select,
        ));

        return $adapter->fetchOne($select);
    }

    /**
     * Get sales rule that associated to banner
     *
     * @param int $bannerId
     * @return array
     */
    public function getRelatedSalesRule($bannerId)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->_salesRuleTable, array())
            ->where('banner_id = ?', $bannerId);
        if (!$this->_isSalesRuleJoined) {
            $select->join(
                array('rules' => $this->getTable('salesrule')),
                $this->_salesRuleTable . '.rule_id = rules.rule_id',
                array('rule_id')
            );
            $this->_isSalesRuleJoined = true;
        }
        $rules = $adapter->fetchCol($select);
        return $rules;
    }

    /**
     * Get catalog rule that associated to banner
     *
     * @param int $bannerId
     * @return array
     */
    public function getRelatedCatalogRule($bannerId)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->_catalogRuleTable, array())
            ->where('banner_id = ?', $bannerId);
        if (!$this->_isCatalogRuleJoined) {
            $select->join(
                array('rules' => $this->getTable('catalogrule')),
                $this->_catalogRuleTable . '.rule_id = rules.rule_id',
                array('rule_id')
            );
            $this->_isCatalogRuleJoined = true;
        }

        $rules = $adapter->fetchCol($select);
        return $rules;
    }

    /**
     * Get banners that associated to catalog rule
     *
     * @param int $ruleId
     * @return array
     */
    public function getRelatedBannersByCatalogRuleId($ruleId)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->_catalogRuleTable, array('banner_id'))
            ->where('rule_id = ?', $ruleId);
        return $adapter->fetchCol($select);
    }

    /**
     * Get banners that associated to sales rule
     *
     * @param int $ruleId
     * @return array
     */
    public function getRelatedBannersBySalesRuleId($ruleId)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->_salesRuleTable, array('banner_id'))
            ->where('rule_id = ?', $ruleId);
        return $adapter->fetchCol($select);
    }

    /**
     * Bind specified banners to catalog rule by rule id
     *
     * @param int $ruleId
     * @param array $banners
     * @return Magento_Banner_Model_Resource_Banner
     */
    public function bindBannersToCatalogRule($ruleId, $banners)
    {
        $adapter = $this->_getWriteAdapter();
        foreach ($banners as $bannerId) {
            $adapter->insertOnDuplicate(
                $this->_catalogRuleTable,
                array('banner_id' => $bannerId, 'rule_id' => $ruleId),
                array('rule_id')
            );
        }

        if (empty($banners)) {
            $banners = array(0);
        }

        $adapter->delete($this->_catalogRuleTable,
            array('rule_id = ?' => $ruleId, 'banner_id NOT IN (?)' => $banners)
        );
        return $this;
    }

    /**
     * Bind specified banners to sales rule by rule id
     *
     * @param int $ruleId
     * @param array $banners
     * @return Magento_Banner_Model_Resource_Banner
     */
    public function bindBannersToSalesRule($ruleId, $banners)
    {
        $adapter = $this->_getWriteAdapter();
        foreach ($banners as $bannerId) {
            $adapter->insertOnDuplicate(
                $this->_salesRuleTable,
                array('banner_id' => $bannerId, 'rule_id' => $ruleId),
                array('rule_id')
            );
        }

        if (empty($banners)) {
            $banners = array(0);
        }

        $adapter->delete($this->_salesRuleTable,
            array('rule_id = ?' => $ruleId, 'banner_id NOT IN (?)' => $banners)
        );
        return $this;
    }

    /**
     * Get real existing banner ids by specified ids
     *
     * @param array $bannerIds
     * @param bool $isActive if true then only active banners will be get
     * @return array
     */
    public function getExistingBannerIdsBySpecifiedIds($bannerIds, $isActive = true)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable(), array('banner_id'))
            ->where('banner_id IN (?)', $bannerIds);
        if ($isActive) {
            $select->where('is_enabled = ?', (int)$isActive);
        }
        return array_intersect($bannerIds, $adapter->fetchCol($select));
    }

    /**
     * Get banners content per store view
     *
     * @param array $bannerIds
     * @param int $storeId
     * @return array
     */
    public function getBannersContent(array $bannerIds, $storeId)
    {
        $result = array();
        foreach ($bannerIds as $bannerId) {
            $bannerContent = $this->getStoreContent($bannerId, $storeId);
            if (!empty($bannerContent)) {
                $result[$bannerId] = $bannerContent;
            }
        }
        return $result;
    }

    /**
     * Get banners IDs that related to sales rule and satisfy conditions
     *
     * @param array $appliedRules
     * @return array
     */
    public function getSalesRuleRelatedBannerIds(array $appliedRules)
    {
        /** @var Magento_Banner_Model_Resource_Salesrule_Collection $collection */
        $collection = Mage::getResourceModel('Magento_Banner_Model_Resource_Salesrule_Collection');
        $collection->addRuleIdsFilter($appliedRules);
        return $collection->getColumnValues('banner_id');
    }

    /**
     * Get banners IDs that related to sales rule and satisfy conditions
     *
     * @param int $websiteId
     * @param int $customerGroupId
     * @return array
     */
    public function getCatalogRuleRelatedBannerIds($websiteId, $customerGroupId)
    {
        /** @var Magento_Banner_Model_Resource_Catalogrule_Collection $collection */
        $collection = Mage::getResourceModel('Magento_Banner_Model_Resource_Catalogrule_Collection');
        $collection->addWebsiteCustomerGroupFilter($websiteId, $customerGroupId);
        return $collection->getColumnValues('banner_id');
    }

    /**
     * Prepare banner types for saving
     *
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_Banner_Model_Resource_Banner
     */
    protected function _beforeSave(Magento_Core_Model_Abstract $object)
    {
        $types = $object->getTypes();
        if (empty($types)) {
            $types = null;
        } elseif (is_array($types)) {
            $types = implode(',', $types);
        }
        if (empty($types)) {
            $types = null;
        }
        $object->setTypes($types);
        return parent::_beforeSave($object);
    }
}
