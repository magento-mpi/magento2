<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customer and Customer Segment Report Collection
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CustomerSegment\Model\Resource\Report\Customer;

class Collection extends \Magento\Customer\Model\Resource\Customer\Collection
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
     * @var \Magento\DB\Select
     */
    protected $_subQuery = null;

    /**
     * Websites array for filter
     *
     * @var array
     */
    protected $_websites = null;

    /**
     * Add filter by segment(s)
     *
     * @param \Magento\CustomerSegment\Model\Segment|integer $segment
     * @return \Magento\CustomerSegment\Model\Resource\Report\Customer\Collection
     */
    public function addSegmentFilter($segment)
    {
        if ($segment instanceof \Magento\CustomerSegment\Model\Segment) {
            $segment = $segment->getId() ? $segment->getId() : $segment->getMassactionIds();
        }

        $this->_subQuery = $this->getViewMode() ==
            \Magento\CustomerSegment\Model\Segment::VIEW_MODE_INTERSECT_CODE ? $this->_getIntersectQuery(
                $segment
            ) : $this->_getUnionQuery(
            $segment
        );

        return $this;
    }

    /**
     * Add filter by websites
     *
     * @param int|null|array $websites
     * @return \Magento\CustomerSegment\Model\Resource\Report\Customer\Collection
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
     * @return \Magento\DB\Select
     */
    protected function _getUnionQuery($segment)
    {
        $select = clone $this->getSelect();
        $select->reset();
        $select->from(
            $this->getTable('magento_customersegment_customer'),
            'customer_id'
        )->where(
            'segment_id IN(?)',
            $segment
        )->where(
            'e.entity_id = customer_id'
        );
        return $select;
    }

    /**
     * Rerieve intersect sub-query
     *
     * @param array $segment
     * @return \Magento\DB\Select
     */
    protected function _getIntersectQuery($segment)
    {
        $select = clone $this->getSelect();
        $select->reset();
        $select->from(
            $this->getTable('magento_customersegment_customer'),
            'customer_id'
        )->where(
            'segment_id IN(?)',
            $segment
        )->where(
            'e.entity_id = customer_id'
        )->group(
            'customer_id'
        )->having(
            'COUNT(segment_id) = ?',
            count($segment)
        );
        return $select;
    }

    /**
     * Setter for view mode
     *
     * @param string $mode
     * @return \Magento\CustomerSegment\Model\Resource\Report\Customer\Collection
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
     * @return \Magento\CustomerSegment\Model\Resource\Report\Customer\Collection
     */
    protected function _applyFilters()
    {
        if (!is_null($this->_websites)) {
            $this->_subQuery->where('website_id IN(?)', $this->_websites);
        }
        $this->getSelect()->where('e.entity_id IN(?)', new \Zend_Db_Expr($this->_subQuery));
        return $this;
    }

    /**
     * Applying delayed filters
     *
     * @return \Magento\CustomerSegment\Model\Resource\Report\Customer\Collection
     */
    protected function _beforeLoad()
    {
        $this->_applyFilters();
        return $this;
    }
}
