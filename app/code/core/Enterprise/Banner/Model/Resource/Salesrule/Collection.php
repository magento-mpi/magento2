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
 * Banner Salesrule Resource Collection
 *
 * @category    Enterprise
 * @package     Enterprise_Banner
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Banner_Model_Resource_Salesrule_Collection extends Mage_SalesRule_Model_Resource_Rule_Collection
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
     * @return Enterprise_Banner_Model_Resource_Salesrule_Collection
     */
    public function resetColumns()
    {
        $this->getSelect()->reset();
        return $this;
    }

    /**
     * Set related banners to sales rule
     *
     * @param array $appliedRules
     * @param bool $enabledOnly if true then only enabled banners will be joined
     * @return Enterprise_Banner_Model_Resource_Salesrule_Collection
     */
    public function addBannersFilter($appliedRules, $enabledOnly = false)
    {
        if (!$this->_isBannerFilterAdded) {
            $select = $this->getSelect();
            $select->from(
                array('rule_related_banners' => $this->getTable('enterprise_banner_salesrule')),
                array('banner_id')
            );
            if (empty($appliedRules)) {
                $aplliedRules = array(0);
            }
            $select->where('rule_related_banners.rule_id IN (?)', $appliedRules);
            if ($enabledOnly) {
                $select->join(
                    array('banners' => $this->getTable('enterprise_banner')),
                    'banners.banner_id = rule_related_banners.banner_id AND banners.is_enabled=1',
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
     * @return Enterprise_Banner_Model_Resource_Salesrule_Collection
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
                    $matchedCustomerSegments);
            }
            $this->_isCustomerSegmentFilterAdded = true;
        }
        return $this;
    }
}
