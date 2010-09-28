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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_SalesRule
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Enter description here ...
 *
 * @category    Mage
 * @package     Mage_SalesRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_SalesRule_Model_Mysql4_Rule extends Mage_SalesRule_Model_Resource_Rule
{

    /**
     * Return codes of all product attributes currently used in promo rules for specified customer group and website
     *
     * @param unknown_type $websiteId
     * @param unknown_type $customerGroupId
     * @return mixed
     */
    public function getActiveAttributes($websiteId, $customerGroupId)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from(array('a' => $this->getTable('salesrule/product_attribute')),
                new Zend_Db_Expr('DISTINCT ea.attribute_code'))
            ->joinInner(array('ea' => $this->getTable('eav/attribute')), 'ea.attribute_id = a.attribute_id', '')
            ;
        return $read->fetchAll($select);
    }

    /**
     * Save product attributes currently used in conditions and actions of rule
     *
     * @param Mage_SalesRule_Model_Rule $rule
     * @param mixed $attributes
     * return Mage_SalesRule_Model_Mysql4_Rule
     */
    public function setActualProductAttributes($rule, $attributes)
    {
        $write = $this->_getWriteAdapter();
        $write->delete($this->getTable('salesrule/product_attribute'),
            $write->quoteInto('rule_id=?', $rule->getId()));

        //Getting attribute IDs for attribute codes
        $attributeIds = array();
        $select = $this->_getReadAdapter()->select()
                ->from(array('a'=>$this->getTable('eav/attribute')), array('a.attribute_id'))
                ->where('a.attribute_code IN (?)', array($attributes));
        if ($attributesFound = $this->_getReadAdapter()->fetchAll($select)) {
            foreach ($attributesFound as $attr) {
                $attributeIds[] = $attr['attribute_id'];
            }

            $data = array();
            foreach (explode(',', $rule->getCustomerGroupIds()) as $customerGroupId) {
                foreach (explode(',', $rule->getWebsiteIds()) as $websiteId) {
                    foreach ($attributeIds as $attribute) {
                        $data[] = array (
                            'rule_id'           => $rule->getId(),
                            'website_id'        => $websiteId,
                            'customer_group_id' => $customerGroupId,
                            'attribute_id'      => $attribute
                        );
                    }
                }
            }
            $write->insertMultiple($this->getTable('salesrule/product_attribute'), $data);
        }
        return $this;
    }
}
