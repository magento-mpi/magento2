<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * SalesRule Resource Coupon
 *
 * @category    Mage
 * @package     Mage_SalesRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_SalesRule_Model_Resource_Coupon extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Constructor adds unique fields
     */
    protected function _construct()
    {
        $this->_init('salesrule_coupon', 'coupon_id');
        $this->addUniqueField(array(
            'field' => 'code',
            'title' => __('Coupon with the same code')
        ));
    }

    /**
     * Perform actions before object save
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    public function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getExpirationDate()) {
            $object->setExpirationDate(null);
        } else if ($object->getExpirationDate() instanceof Zend_Date) {
            $object->setExpirationDate($object->getExpirationDate()->toString(Magento_Date::DATETIME_INTERNAL_FORMAT));
        }

        // maintain single primary coupon per rule
        $object->setIsPrimary($object->getIsPrimary() ? 1 : null);

        return parent::_beforeSave($object);
    }

    /**
     * Load primary coupon (is_primary = 1) for specified rule
     *
     *
     * @param Mage_SalesRule_Model_Coupon $object
     * @param Mage_SalesRule_Model_Rule|int $rule
     * @return unknown
     */
    public function loadPrimaryByRule(Mage_SalesRule_Model_Coupon $object, $rule)
    {
        $read = $this->_getReadAdapter();

        if ($rule instanceof Mage_SalesRule_Model_Rule) {
            $ruleId = $rule->getId();
        } else {
            $ruleId = (int)$rule;
        }

        $select = $read->select()->from($this->getMainTable())
            ->where('rule_id = :rule_id')
            ->where('is_primary = :is_primary');

        $data = $read->fetchRow($select, array(':rule_id' => $ruleId, ':is_primary' => 1));

        if (!$data) {
            return false;
        }

        $object->setData($data);

        $this->_afterLoad($object);
        return true;
    }

    /**
     * Check if code exists
     *
     * @param string $code
     * @return bool
     */
    public function exists($code)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select();
        $select->from($this->getMainTable(), 'code');
        $select->where('code = :code');

        if ($read->fetchOne($select, array('code' => $code)) === false) {
            return false;
        }
        return true;
    }

    /**
     * Update auto generated Specific Coupon if it's rule changed
     *
     * @param Mage_SalesRule_Model_Rule $rule
     * @return Mage_SalesRule_Model_Resource_Coupon
     */
    public function updateSpecificCoupons(Mage_SalesRule_Model_Rule $rule)
    {
        if (!$rule || !$rule->getId() || !$rule->hasDataChanges()) {
            return $this;
        }

        $updateArray = array();
        if ($rule->dataHasChangedFor('uses_per_coupon')) {
            $updateArray['usage_limit'] = $rule->getUsesPerCoupon();
        }

        if ($rule->dataHasChangedFor('uses_per_customer')) {
            $updateArray['usage_per_customer'] = $rule->getUsesPerCustomer();
        }

        $ruleNewDate = new Zend_Date($rule->getToDate());
        $ruleOldDate = new Zend_Date($rule->getOrigData('to_date'));

        if ($ruleNewDate->compare($ruleOldDate)) {
            $updateArray['expiration_date'] = $rule->getToDate();
        }

        if (!empty($updateArray)) {
            $this->_getWriteAdapter()->update(
                $this->getTable('salesrule_coupon'),
                $updateArray,
                array('rule_id = ?' => $rule->getId())
            );
        }

        return $this;
    }
}
