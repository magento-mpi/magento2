<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Tax
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tax Calculation Resource Model
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tax_Model_Mysql4_Calculation extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_setMainTable('tax/tax_calculation');
    }

    public function deleteByRuleId($ruleId)
    {
        $conn = $this->_getWriteAdapter();
        $where = $conn->quoteInto('tax_calculation_rule_id = ?', $ruleId);
        $conn->delete($this->getMainTable(), $where);
    }

    public function getDistinct($field, $ruleId)
    {
        $select = $this->_getReadAdapter()->select();
        $select->from($this->getMainTable(), $field)->where('tax_calculation_rule_id = ?', $ruleId);
        return $this->_getReadAdapter()->fetchCol($select);
    }

    public function getRate($request)
    {
        return $this->_calculateRate($this->_getRates($request));
    }

    protected function _getRates($request)
    {
        $select = $this->_getReadAdapter()->select();
        $select
            ->from(array('main_table'=>$this->getMainTable()))
            ->where('customer_tax_class_id = ?', $request->getCustomerClassId())
            ->where('product_tax_class_id = ?', $request->getProductClassId());

        $select->join(array('rule'=>$this->getTable('tax/tax_calculation_rule')), 'rule.tax_calculation_rule_id = main_table.tax_calculation_rule_id', array('rule.priority'));
        $select->join(array('rate'=>$this->getTable('tax/tax_calculation_rate')), 'rate.tax_calculation_rate_id = main_table.tax_calculation_rate_id', array('value'=>'rate.rate', 'rate.tax_country_id', 'rate.tax_region_id', 'rate.tax_postcode', 'rate.tax_calculation_rate_id'));

        $select
            ->where("rate.tax_country_id = ?", $request->getCountryId())
            ->where("rate.tax_region_id in ('*', '', ?)", $request->getRegionId())
            ->where("rate.tax_postcode in ('*', '', ?)", $request->getPostcode());

        $order = array('rule.priority ASC', 'rule.tax_calculation_rule_id ASC', 'rate.tax_country_id DESC', 'rate.tax_region_id DESC', 'rate.tax_postcode DESC', 'rate.rate DESC');
        $select->order($order);

        return $this->_getReadAdapter()->fetchAll($select);
    }

    protected function _calculateRate($rates)
    {
        $result = 0;
        $currentRate = 0;
        for ($i=0; $i<count($rates); $i++) {
            $rate = $rates[$i];
            $rule = $rate['tax_calculation_rule_id'];
            $value = $rate['value'];
            $priority = $rate['priority'];

            while(isset($rates[$i+1]) && $rates[$i+1]['tax_calculation_rule_id'] == $rule) {
                $i++;
            }

            $currentRate += $value;

            if (!isset($rates[$i+1]) || $rates[$i+1]['priority'] != $priority) {
                $result += (100+$result)*($currentRate/100);
                $currentRate = 0;
            }
        }

        return $result;
    }

    public function getRateIds($request)
    {
        $result = array();
        $rates = $this->_getRates($request);
        for ($i=0; $i<count($rates); $i++) {
            $rate = $rates[$i];
            $rule = $rate['tax_calculation_rule_id'];
            $result[] = $rate['tax_calculation_rate_id'];
            while(isset($rates[$i+1]) && $rates[$i+1]['tax_calculation_rule_id'] == $rule) {
                $i++;
            }
        }
        return $result;
    }
}