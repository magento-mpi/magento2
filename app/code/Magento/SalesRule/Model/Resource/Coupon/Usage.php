<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * SalesRule Model Resource Coupon_Usage
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\SalesRule\Model\Resource\Coupon;

class Usage extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Constructor
     *
     */
    protected function _construct()
    {
        $this->_init('salesrule_coupon_usage', 'coupon_id');
    }

    /**
     * Increment times_used counter
     *
     *
     * @param unknown_type $customerId
     * @param unknown_type $couponId
     */
    public function updateCustomerCouponTimesUsed($customerId, $couponId)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select();
        $select->from($this->getMainTable(), array('times_used'))
                ->where('coupon_id = :coupon_id')
                ->where('customer_id = :customer_id');

        $timesUsed = $read->fetchOne($select, array(':coupon_id' => $couponId, ':customer_id' => $customerId));

        if ($timesUsed > 0) {
            $this->_getWriteAdapter()->update(
                $this->getMainTable(),
                array(
                    'times_used' => $timesUsed + 1
                ),
                array(
                    'coupon_id = ?' => $couponId,
                    'customer_id = ?' => $customerId,
                )
            );
        } else {
            $this->_getWriteAdapter()->insert(
                $this->getMainTable(),
                array(
                    'coupon_id' => $couponId,
                    'customer_id' => $customerId,
                    'times_used' => 1
                )
            );
        }
    }

    /**
     * Load an object by customer_id & coupon_id
     *
     *
     * @param \Magento\Object $object
     * @param unknown_type $customerId
     * @param unknown_type $couponId
     * @return \Magento\SalesRule\Model\Resource\Coupon\Usage
     */
    public function loadByCustomerCoupon(\Magento\Object $object, $customerId, $couponId)
    {
        $read = $this->_getReadAdapter();
        if ($read && $couponId && $customerId) {
            $select = $read->select()
                ->from($this->getMainTable())
                ->where('customer_id =:customet_id')
                ->where('coupon_id = :coupon_id');
            $data = $read->fetchRow($select, array(':coupon_id' => $couponId, ':customet_id' => $customerId));
            if ($data) {
                $object->setData($data);
            }
        }
        if ($object instanceof \Magento\Core\Model\AbstractModel) {
            $this->_afterLoad($object);
        }
        return $this;
    }
}
