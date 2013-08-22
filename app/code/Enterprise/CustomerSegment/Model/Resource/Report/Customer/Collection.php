<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customer and Customer Segment Report Collection
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_CustomerSegment_Model_Resource_Report_Customer_Collection
    extends Magento_Customer_Model_Resource_Customer_Collection
{
    /**
     * View mode
     *
     * @var string
     */
    protected $_viewMode;

    /**
     * Subquery for filter
     *
     * @var Magento_DB_Select
     */
    protected $_subQuery     = null;

    /**
     * Websites array for filter
     *
     * @var array
     */
    protected $_websites     = null;

    /**
     * Add filter by segment(s)
     *
     * @param Enterprise_CustomerSegment_Model_Segment|integer $segment
     * @return Enterprise_CustomerSegment_Model_Resource_Report_Customer_Collection
     */
    public function addSegmentFilter($segment)
    {
        if ($segment instanceof Enterprise_CustomerSegment_Model_Segment) {
            $segment = ($segment->getId()) ? $segment->getId() : $segment->getMassactionIds();
        }

        $this->_subQuery = ($this->getViewMode() == Enterprise_CustomerSegment_Model_Segment::VIEW_MODE_INTERSECT_CODE)
            ? $this->_getIntersectQuery($segment)
            : $this->_getUnionQuery($segment);

        return $this;
    }

    /**
     * Add filter by websites
     *
     * @param int|null|array $websites
     * @return Enterprise_CustomerSegment_Model_Resource_Report_Customer_Collection
     */
    public function addWebsiteFilter($websites)
    {
        if (is_null($websites)) {
            return $this;
        }
        if (!is_array($websites)) {
            $websites = array($websites);
        }
        $this->_websites = array_unique($websites);
        return $this;
    }

    /**
     * Rerieve union sub-query
     *
     * @param array|int $segment
     * @return Magento_DB_Select
     */
    protected function _getUnionQuery($segment)
    {
        $select = clone $this->getSelect();
        $select->reset();
        $select->from(
            $this->getTable('enterprise_customersegment_customer'),
            'customer_id'
        )
        ->where('segment_id IN(?)', $segment)
        ->where('e.entity_id = customer_id');
        return $select;
    }

    /**
     * Rerieve intersect sub-query
     *
     * @param array $segment
     * @return Magento_DB_Select
     */
    protected function _getIntersectQuery($segment)
    {
        $select = clone $this->getSelect();
        $select->reset();
        $select->from(
            $this->getTable('enterprise_customersegment_customer'),
            'customer_id'
        )
        ->where('segment_id IN(?)', $segment)
        ->where('e.entity_id = customer_id')
        ->group('customer_id')
        ->having('COUNT(segment_id) = ?', count($segment));
        return $select;
    }

    /**
     * Setter for view mode
     *
     * @param string $mode
     * @return Enterprise_CustomerSegment_Model_Resource_Report_Customer_Collection
     */
    public function setViewMode($mode)
    {
        $this->_viewMode = $mode;
        return $this;
    }

    /**
     * Getter fo view mode
     *
     * @return string
     */
    public function getViewMode()
    {
        return $this->_viewMode;
    }

    /**
     * Apply filters
     *
     * @return Enterprise_CustomerSegment_Model_Resource_Report_Customer_Collection
     */
    protected function _applyFilters()
    {
        if (!is_null($this->_websites)) {
            $this->_subQuery->where('website_id IN(?)', $this->_websites);
        }
        $this->getSelect()->where('e.entity_id IN(?)', new Zend_Db_Expr($this->_subQuery));
        return $this;
    }

    /**
     * Applying delayed filters
     *
     * @return Enterprise_CustomerSegment_Model_Resource_Report_Customer_Collection
     */
    protected function _beforeLoad()
    {
        $this->_applyFilters();
        return $this;
    }
}
