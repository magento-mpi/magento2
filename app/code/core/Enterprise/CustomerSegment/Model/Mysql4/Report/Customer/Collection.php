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
 * @package    Enterprise_CustomerSegment
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
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
     * Initialize select and join customer segment table
     *
     * @return Enterprise_CustomerSegment_Model_Mysql4_Report_Customer_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->join(
            array('customer_count_table' => $this->getTable('enterprise_customersegment/customer')),
            'customer_count_table.customer_id = e.entity_id',
            array()
        );
        return $this;
    }

    /**
     * Add filter by segment id
     *
     * @param Enterprise_CustomerSegment_Model_Segment|integer $segment
     * @return Enterprise_CustomerSegment_Model_Mysql4_Report_Customer_Collection
     */
    public function addSegmentFilter($segment)
    {
        if ($segment instanceof Enterprise_CustomerSegment_Model_Segment) {
            $segment = $segment->getId();
        }
        $this->getSelect()
            ->where('customer_count_table.segment_id = ?', $segment);
        return $this;
    }
}