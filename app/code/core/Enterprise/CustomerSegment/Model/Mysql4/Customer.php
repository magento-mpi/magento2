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

class Enterprise_CustomerSegment_Model_Mysql4_Customer extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Intialize resource model
     *
     * @return void
     */
    protected function _construct ()
    {
        $this->_init('enterprise_customersegment/customer', 'customer_id');
    }

    /**
     * Save relations between customer id and segment ids
     *
     * @param int $customerId
     * @param array $segmentIds
     * @return Enterprise_CustomerSegment_Model_Mysql4_Customer
     */
    public function addCustomerToSegments($customerId, $segmentIds)
    {
        $data = array();
        $now = $this->formatDate(time(), true);
        foreach ($segmentIds as $segmentId) {
            $data = array(
                'segment_id'    => $segmentId,
                'customer_id'   => $customerId,
                'added_date'    => $now,
                'updated_date'  => $now,
            );
            $this->_getWriteAdapter()->insertOnDuplicate($this->getMainTable(), $data, array('updated_date'));
        }
        return $this;
    }

    /**
     * Remove relations between customer id and segment ids
     *
     * @param int $customerId
     * @param array $segmentIds
     * @return Enterprise_CustomerSegment_Model_Mysql4_Customer
     */
    public function removeCustomerFromSegments($customerId, $segmentIds)
    {
        if (!empty($segmentIds)) {
            $adapter = $this->_getWriteAdapter();
            $condition = $adapter->quoteInto('customer_id=? AND ', $customerId)
                . $adapter->quoteInto('segment_id IN (?)', $segmentIds);
            $adapter->delete($this->getMainTable(), $condition);
        }
        return $this;
    }

    /**
     * Get segment ids assigned to customer id
     *
     * @param   int $customerId
     * @return  array
     */
    public function getCustomerSegments($customerId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), 'segment_id')
            ->where('customer_id = ?', $customerId);
        return $this->_getReadAdapter()->fetchCol($select);
    }
}
