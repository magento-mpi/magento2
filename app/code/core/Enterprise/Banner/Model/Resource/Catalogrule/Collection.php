<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Banner Catalogrule Resource Collection
 *
 * @category    Enterprise
 * @package     Enterprise_Banner
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Banner_Model_Resource_Catalogrule_Collection extends Mage_CatalogRule_Model_Resource_Rule_Collection
{
    /**
     * Define if banner filter is already called
     *
     * @var bool
     */
    protected $_isBannerFilterAdded              = false;

    /**
     * Define if customer segment filter is already called
     *
     * @var bool
     */
    protected $_isCustomerSegmentFilterAdded     = false;

    /**
     * Reset collection select
     *
     * @return Enterprise_Banner_Model_Resource_Catalogrule_Collection
     */
    public function resetSelect()
    {
        $this->getSelect()->reset();
        return $this;
    }

    /**
     * Apply only valid rules
     *
     * @param int $websiteId
     * @param int $customerGroupId
     * @return Enterprise_Banner_Model_Resource_Catalogrule_Collection
     */
    public function addAppliedRuleFilter($websiteId, $customerGroupId)
    {
        $this->getSelect()
            ->from(array('rule_group_website' => $this->getTable('catalogrule_group_website')), array())
            ->where('rule_group_website.customer_group_id = ?', $customerGroupId)
            ->where('rule_group_website.website_id = ?', $websiteId);
        return $this;
    }

    /**
     * Set related banners to catalog rule
     *
     * @param bool $enabledOnly if true then only enabled banners will be joined
     * @return Enterprise_Banner_Model_Resource_Catalogrule_Collection
     */
    public function addBannersFilter($enabledOnly = false)
    {
        if (!$this->_isBannerFilterAdded) {
            $select = $this->getSelect();
            $select->join(
                array('rule_related_banners' => $this->getTable('enterprise_banner_catalogrule')),
                'rule_related_banners.rule_id = rule_group_website.rule_id',
                array('banner_id')
            );

            if ($enabledOnly) {
                $select->join(
                    array('banners' => $this->getTable('enterprise_banner')),
                    'banners.banner_id = rule_related_banners.banner_id AND banners.is_enabled = 1',
                    array()
                );
            }
            $select->group('rule_related_banners.banner_id');

            $this->_isBannerFilterAdded = true;
        }
        return $this;
    }

    /**
     * Filter banners by customer segments
     *
     * @param array $matchedCustomerSegments
     * @return Enterprise_Banner_Model_Resource_Catalogrule_Collection
     */
    public function addCustomerSegmentFilter($matchedCustomerSegments)
    {
        if (!$this->_isCustomerSegmentFilterAdded && !empty($matchedCustomerSegments)) {
            $select = $this->getSelect();
            $select->joinLeft(
                array('banner_segments' => $this->getTable('enterprise_banner_customersegment')),
                'banners.banner_id = banner_segments.banner_id',
                array()
            );
            
            if (empty($matchedCustomerSegments)) {
                $select->where('banner_segments.segment_id IS NULL');
            } else {
                $select->where('banner_segments.segment_id IS NULL OR banner_segments.segment_id IN (?)',
                    $matchedCustomerSegments
                );
            }

            $this->_isCustomerSegmentFilterAdded = true;
        }
        return $this;
    }
}
