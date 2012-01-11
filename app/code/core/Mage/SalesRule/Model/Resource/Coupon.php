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
     *
     */
    protected function _construct()
    {
        $this->_init('salesrule_coupon', 'coupon_id');
        $this->addUniqueField(array(
            'field' => 'code',
            'title' => Mage::helper('Mage_SalesRule_Helper_Data')->__('Coupon with the same code')
        ));
    }

    /**
     * Perform actions before object save
     *
     * @param Varien_Object $object
     * @return unknown
     */
    public function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getExpirationDate()) {
            $object->setExpirationDate(new Zend_Db_Expr('NULL'));
        } else if ($object->getExpirationDate() instanceof Zend_Date) {
            $object->setExpirationDate($object->getExpirationDate()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
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
     * @param unknown_type $rule
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
}
