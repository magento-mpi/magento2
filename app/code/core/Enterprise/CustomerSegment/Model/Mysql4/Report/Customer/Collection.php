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
 * @package     Enterprise_CustomerSegment
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Customer and Customer Segment Report Collection
 *
 * @category   Enterprise
 * @package    Enterprise_CustomerSegment
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_CustomerSegment_Model_Mysql4_Report_Customer_Collection
    extends Mage_Customer_Model_Entity_Customer_Collection
{

    /**
     * View mode
     *
     * @var string
     */
    protected $_viewMode;

    /**
     * Add filter by segment(s)
     *
     * @param Enterprise_CustomerSegment_Model_Segment|integer $segment
     * @return Enterprise_CustomerSegment_Model_Mysql4_Report_Customer_Collection
     */
    public function addSegmentFilter($segment)
    {
        if ($segment instanceof Enterprise_CustomerSegment_Model_Segment) {
            $segment = ($segment->getId()) ? $segment->getId() : $segment->getMassactionIds();
        }

        $subQuery = ($this->getViewMode() == Enterprise_CustomerSegment_Model_Segment::VIEW_MODE_INTERSECT_CODE)
            ? $this->_getIntersectQuery($segment)
            : $this->_getUnionQuery($segment);

        $this->getSelect()
            ->where('e.entity_id IN(?)', new Zend_Db_Expr($subQuery));
        return $this;
    }

    /**
     * Rerieve union sub-query
     *
     * @param array|int $segment
     * @return Varien_Db_Select
     */
    protected function _getUnionQuery($segment)
    {
        $select = clone $this->getSelect();
        $select->reset();
        $select->from(
            $this->getTable('enterprise_customersegment/customer'),
            'customer_id'
        )
        ->where('segment_id IN(?)', $segment);
        return $select;
    }

    /**
     * Rerieve intersect sub-query
     *
     * @param array $segment
     * @return Varien_Db_Select
     */
    protected function _getIntersectQuery($segment)
    {
        $select = clone $this->getSelect();
        $select->reset();
        $select->from(
            $this->getTable('enterprise_customersegment/customer'),
            'customer_id'
        )
        ->where('segment_id IN(?)', $segment)
        ->group('customer_id')
        ->having('COUNT(segment_id) = ?', count($segment));
        return $select;
    }

    /**
     * Setter for view mode
     *
     * @param string $mode
     * @return Enterprise_CustomerSegment_Model_Mysql4_Report_Customer_Collection
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
}
