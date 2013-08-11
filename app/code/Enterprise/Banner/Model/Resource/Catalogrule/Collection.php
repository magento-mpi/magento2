<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Collection of banner <-> catalog rule associations
 */
class Enterprise_Banner_Model_Resource_Catalogrule_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'enterprise_banner_catalogrule_collection';

    /**
     * @var string
     */
    protected $_eventObject = 'collection';

    /**
     * Define collection item type and corresponding table
     */
    protected function _construct()
    {
        $this->_init('Magento_Object', 'Magento_CatalogRule_Model_Resource_Rule');
        $this->setMainTable('enterprise_banner_catalogrule');
    }

    /**
     * Filter out disabled banners
     *
     * @return Magento_Core_Model_Resource_Db_Collection_Abstract
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()
            ->join(
                array('banner' => $this->getTable('enterprise_banner')),
                'banner.banner_id = main_table.banner_id AND banner.is_enabled = 1',
                array()
            )
            ->group('main_table.banner_id')
        ;
        return $this;
    }

    /**
     * Add website id and customer group id filter to the collection
     *
     * @param int $websiteId
     * @param int $customerGroupId
     * @return Enterprise_Banner_Model_Resource_Catalogrule_Collection
     */
    public function addWebsiteCustomerGroupFilter($websiteId, $customerGroupId)
    {
        $this->getSelect()
            ->join(
                array('rule_group_website' => $this->getTable('catalogrule_group_website')),
                'rule_group_website.rule_id = main_table.rule_id',
                array()
            )
            ->where('rule_group_website.customer_group_id = ?', $customerGroupId)
            ->where('rule_group_website.website_id = ?', $websiteId)
        ;
        return $this;
    }
}
