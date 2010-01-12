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
 * @package     Enterprise_TargetRule
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


class Enterprise_TargetRule_Model_Rule_Condition_Product_Attributes
    extends Mage_CatalogRule_Model_Rule_Condition_Product
{
    /**
     * All attribute values as array in form:
     * array(
     *   [entity_id_1] => array(
     *          [store_id_1] => store_value_1,
     *          [store_id_2] => store_value_2,
     *          ...
     *          [store_id_n] => store_value_n
     *   ),
     *   ...
     * )
     *
     * Will be set only for not global scope attribute
     *
     * @var array
     */
    protected $_entityAttributeValues = null;

    /**
     * Attribute property that defines whether to use it for target rules
     *
     * @var string
     */
    protected $_isUsedForRuleProperty = 'is_used_for_target_rules';

    /**
     * Target rule codes that do not allowed to select
     *
     * @var array
     */
    protected $_disabledTargetRuleCodes = array(
        'status' // products with status 'disabled' cannot be shown as related/cross-sells/up-sells thus rule code is useless
    );

    /**
     * Set condition type and value
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setType('enterprise_targetrule/rule_condition_product_attributes');
        $this->setValue(null);
    }

    /**
     * Collect validated attributes
     *
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $productCollection
     * @return Enterprise_TargetRule_Model_Rule_Condition_Product_Attributes
     */
    public function collectValidatedAttributes($productCollection)
    {
        if ($this->getAttributeObject()->isScopeGlobal()) {
            return parent::collectValidatedAttributes($productCollection);
        }

        $this->_entityAttributeValues = Mage::getResourceModel('catalog/product_collection')->getAllAttributeValues($this->getAttribute());

        return $this;
    }

    /**
     * Prepare child rules option list
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $attributes = $this->loadAttributeOptions()->getAttributeOption();
        $conditions = array();
        foreach ($attributes as $code => $label) {
            if (! in_array($code, $this->_disabledTargetRuleCodes)) {
                $conditions[] = array(
                    'value' => $this->getType() . '|' . $code,
                    'label' => $label
                );
            }
        }

        return array(
            'value' => $conditions,
            'label' => Mage::helper('enterprise_targetrule')->__('Product Attributes')
        );
    }

    /**
     * Validate product attrbute value for condition
     *
     * @param Varien_Object $object
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        $attrCode = $this->getAttribute();

        if (! isset($this->_entityAttributeValues[$object->getId()]) || $attrCode == 'category_ids') {
            return parent::validate($object);
        } else {
            $result       = false; // any valid value will set it to TRUE
            $oldAttrValue = $object->hasData($attrCode) ? $object->getData($attrCode) : null; // remember old attribute state

            foreach ($this->_entityAttributeValues[$object->getId()] as $storeId => $value) {
                $object->setData($attrCode, $value);

                $result |= parent::validate($object);

                if ($result) {
                    break;
                }
            }

            if (is_null($oldAttrValue)) {
                $object->unsetData($attrCode);
            } else {
                $object->setData($attrCode, $oldAttrValue);
            }

            return (bool) $result;
        }
    }
}
