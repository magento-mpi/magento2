<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product attribute value condition
 */
class Enterprise_Reminder_Model_Rule_Condition_Wishlist_Attributes
    extends Mage_Rule_Model_Condition_Product_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('Enterprise_Reminder_Model_Rule_Condition_Wishlist_Attributes');
        $this->setValue(null);
    }

    /**
     * Customize default operator input by type mapper for some types
     * @return array
     */
    public function getDefaultOperatorInputByType()
    {
        if (null === $this->_defaultOperatorInputByType) {
            parent::getDefaultOperatorInputByType();
            $this->_defaultOperatorInputByType['numeric'] = array('==', '!=', '>=', '>', '<=', '<');
            $this->_defaultOperatorInputByType['string'] = array('==', '!=', '{}', '!{}');
            $this->_defaultOperatorInputByType['category'] = array('{}', '!{}');
        }
        return $this->_defaultOperatorInputByType;
    }

    /**
     * Get input type for attribute operators.
     *
     * @return string
     */
    public function getInputType()
    {
        if (!is_object($this->getAttributeObject())) {
            return 'string';
        }
        if ($this->getAttributeObject()->getAttributeCode()=='category_ids') {
            return 'category';
        }
        $input = $this->getAttributeObject()->getFrontendInput();
        switch ($input) {
            case 'select':
            case 'multiselect':
            case 'date':
                return $input;
            default:
                return 'string';
        }
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $attributes = $this->loadAttributeOptions()->getAttributeOption();
        $conditions = array();
        foreach ($attributes as $code => $label) {
            $conditions[] = array('value' => $this->getType() . '|' . $code, 'label' => $label);
        }

        return array(
            'value' => $conditions,
            'label' => Mage::helper('Enterprise_Reminder_Helper_Data')->__('Product Attributes')
        );
    }

    /**
     * Get HTML of condition string
     *
     * @return string
     */
    public function asHtml()
    {
        return Mage::helper('Enterprise_Reminder_Helper_Data')->__('Product %s', strtolower(parent::asHtml()));
    }

    /**
     * Get product attribute object
     *
     * @return Mage_Catalog_Model_Resource_Eav_Attribute
     */
    public function getAttributeObject()
    {
        return Mage::getSingleton('Mage_Eav_Model_Config')->getAttribute('catalog_product', $this->getAttribute());
    }

    /**
     * Get resource
     *
     * @return Enterprise_Reminder_Model_Resource_Rule
     */
    public function getResource()
    {
        return Mage::getResourceSingleton('Enterprise_Reminder_Model_Resource_Rule');
    }

    /**
     * Get used subfilter type
     *
     * @return string
     */
    public function getSubfilterType()
    {
        return 'product';
    }

    /**
     * Apply product attribute subfilter to parent/base condition query
     *
     * @param string $fieldName base query field name
     * @param bool $requireValid strict validation flag
     * @param $website
     * @return string
     */
    public function getSubfilterSql($fieldName, $requireValid, $website)
    {
        $attribute = $this->getAttributeObject();
        $table = $attribute->getBackendTable();

        $resource = $this->getResource();
        $select = $resource->createSelect();
        $select->from(array('main' => $table), array('entity_id'));

        if ($attribute->getAttributeCode() == 'category_ids') {
            $condition = $resource->createConditionSql(
                'cat.category_id', $this->getOperatorForValidate(), $this->getValueParsed()
            );
            $categorySelect = $resource->createSelect();
            $categorySelect->from(array('cat' => $resource->getTable('catalog_category_product')), 'product_id')
                ->where($condition);
            $condition = 'main.entity_id IN (' . $categorySelect . ')';
        } elseif ($attribute->isStatic()) {
            $attrCol = $select->getAdapter()->quoteColumnAs('main.' . $attribute->getAttributeCode(), null);
            $condition = $this->getResource()->createConditionSql(
                $attrCol, $this->getOperator(), $this->getValue()
            );
        } else {
            $select->where('main.attribute_id = ?', $attribute->getId());
            $select->join(
                    array('store' => $this->getResource()->getTable('core_store')),
                    'main.store_id=store.store_id',
                    array())
                ->where('store.website_id IN(?)', array(0, $website));
            $condition = $this->getResource()->createConditionSql(
                'main.value', $this->getOperator(), $this->getValue()
            );
        }
        $select->where($condition);
        $inOperator = ($requireValid ? 'IN' : 'NOT IN');
        return sprintf("%s %s (%s)", $fieldName, $inOperator, $select);
    }
}