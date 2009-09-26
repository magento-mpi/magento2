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
 * @package     Enterprise_Banner
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_Banner_Model_Mysql4_Banner extends Mage_Core_Model_Mysql4_Abstract
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
     * Initialize banner resource model
     *
     */
    protected function _construct()
    {
        $this->_init('enterprise_banner/banner', 'banner_id');
        $this->_salesRuleTable = $this->getTable('enterprise_banner/salesrule');
        $this->_catalogRuleTable = $this->getTable('enterprise_banner/catalogrule');
        $this->_contentsTable = $this->getTable('enterprise_banner/content');
    }

    /**
     * Save banner contents for different store views
     *
     * @param   int $bannerId
     * @param   array $contents
     * @param   array $notuse
     *
     * @return  Enterprise_Banner_Model_Mysql4_Banner
     */
    public function saveStoreContents($bannerId, $contents, $notuse = array())
    {
        $deleteContentsByStores = array();
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
            }
            else {
                $deleteContentsByStores[] = $storeId;
            }
        }
        if (!empty($deleteContentsByStores) || !empty($notuse)) {
            $adapter->delete($this->_contentsTable,
                $adapter->quoteInto('banner_id=? AND ', $bannerId) . $adapter->quoteInto('store_id IN (?)',
                array_merge($deleteContentsByStores, array_keys($notuse)))
            );
        }
        return $this;
    }

    /**
     * Delete unckecked catalog rules
     *
     * @param int $bannerId
     * @param array $rules
     *
     * @return Enterprise_Banner_Model_Mysql4_Banner
     */
    public function saveCatalogRules($bannerId, $rules)
    {
        $adapter = $this->_getWriteAdapter();
        if (empty($rules)) {
            $rules = array(0);
        }
        else {
            foreach ($rules as $ruleId) {
                $adapter->insertOnDuplicate(
                    $this->_catalogRuleTable,
                    array('banner_id' => $bannerId, 'rule_id' => $ruleId),
                    array('rule_id')
                );
            }
        }
        $adapter->delete($this->_catalogRuleTable,
            $adapter->quoteInto('banner_id=? AND ', $bannerId) . $adapter->quoteInto('rule_id NOT IN (?)', $rules)
        );
        return $this;
    }

    /**
     * Delete unckecked sale rules
     *
     * @param int $bannerId
     * @param array $rules
     * @return Enterprise_Banner_Model_Mysql4_Banner
     */
    public function saveSalesRules($bannerId, $rules)
    {
        $adapter = $this->_getWriteAdapter();
        if (empty($rules)) {
            $rules = array(0);
        }
        else {
            foreach ($rules as $ruleId) {
                $adapter->insertOnDuplicate(
                    $this->_salesRuleTable,
                    array('banner_id' => $bannerId, 'rule_id' => $ruleId),
                    array('rule_id')
                );
            }
        }
        $adapter->delete($this->_salesRuleTable,
            $adapter->quoteInto('banner_id=? AND ', $bannerId) . $adapter->quoteInto('rule_id NOT IN (?)', $rules)
        );
        return $this;
    }

    /**
     * Get all existing banner contents
     *
     * @param   int $bannerId
     * @return  array
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
     * @param   int $bannerId
     * @param   int $storeId
     * @return  string
     */
    public function getStoreContent($bannerId, $storeId)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->_contentsTable, 'banner_content')
            ->where('banner_id=?', $bannerId)
            ->where('store_id IN(?)', array($storeId, 0))
            ->order('store_id DESC');
        return $adapter->fetchOne($select);
    }

    /**
     * Get sales rule that associated to banner
     *
     * @param   int $bannerId
     * @return  array
     */
    public function getRelatedSalesRule($bannerId)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->_salesRuleTable, array())
            ->join(
                array('rules' => $this->getTable('salesrule/rule')),
                $this->_salesRuleTable . '.rule_id = `rules`.rule_id',
                array('rule_id')
            )
            ->where('banner_id=?', $bannerId);
        $rules = $adapter->fetchCol($select);
        return $rules;
    }

    /**
     * Get catalog rule that associated to banner
     *
     * @param   int $banner
     * @return  array
     */
    public function getRelatedCatalogRule($bannerId)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->_catalogRuleTable, array())
            ->join(
                array('rules' => $this->getTable('catalogrule/rule')),
                $this->_catalogRuleTable . '.rule_id = `rules`.rule_id',
                array('rule_id')
            )
            ->where('banner_id=?', $bannerId);
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
            ->where('rule_id=?', $ruleId);
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
            ->where('rule_id=?', $ruleId);
        return $adapter->fetchCol($select);
    }

    /**
     * Bind specified banners to catalog rule by rule id
     *
     * @param int $ruleId
     * @param array $banners
     *
     * @return Enterprise_Banner_Model_Mysql4_Banner
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
            $adapter->quoteInto('rule_id=? AND ', $ruleId) . $adapter->quoteInto('banner_id NOT IN (?)', $banners)
        );
        return $this;
    }

    /**
     * Bind specified banners to sales rule by rule id
     *
     * @param int $ruleId
     * @param array $banners
     *
     * @return Enterprise_Banner_Model_Mysql4_Banner
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
            $adapter->quoteInto('rule_id=? AND ', $ruleId) . $adapter->quoteInto('banner_id NOT IN (?)', $banners)
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
        return $adapter->fetchCol($select);
    }

    /**
     * Get banners content per store view
     *
     * @param array $bannerIds
     * @param int $storeId
     * @return array
     */
    public function getBannersContent($bannerIds, $storeId)
    {
        $content = array();
        foreach ($bannerIds as $_id) {
            $_content = $this->getStoreContent($_id, $storeId);
            if (!empty($_content)) {
                $content[$_id] = $_content;
            }
        }
        return $content;
    }

    /**
     * Get banners IDs that related to sales rule and satisfy conditions
     *
     * @param int $websiteId
     * @param int $customerGroupId
     * @param int $customerId
     * @return array
     */
    public function getSalesRuleRelatedBannerIds($websiteId, $customerGroupId, $customerId)
    {
        $adapter = $this->_getReadAdapter();
        $collection = Mage::getResourceModel('enterprise_banner/salesrule_collection');
        $collection->resetColumns()
               ->setRuleValidationFilter($websiteId, $customerGroupId, $customerId)
               ->setBannersFilter(true);
        return $adapter->fetchCol($collection->getSelect());
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
        $adapter = $this->_getReadAdapter();
        $collection = Mage::getResourceModel('enterprise_banner/catalogrule_collection');
        $collection->resetColumns()
               ->setRuleValidationFilter($websiteId, $customerGroupId)
               ->setBannersFilter(true);
        return $adapter->fetchCol($collection->getSelect());
    }
}
