<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TargetRule\Model\Actions\Condition\Product;

/**
 * TargetRule Action Product Attributes Condition Model
 *
 * @category Magento
 * @package  Magento_TargetRule
 * @author   Magento Core Team <core@magentocommerce.com>
 */
class Attributes extends \Magento\TargetRule\Model\Rule\Condition\Product\Attributes
{
    /**
     * Value type values constants
     *
     */
    const VALUE_TYPE_CONSTANT = 'constant';

    const VALUE_TYPE_SAME_AS = 'same_as';

    const VALUE_TYPE_CHILD_OF = 'child_of';

    /**
     * @var \Magento\Catalog\Model\Product\Type
     */
    protected $_type;

    /**
     * @var \Magento\Rule\Block\Editable
     */
    protected $_editable;

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Backend\Helper\Data $backendData
     * @param \Magento\Eav\Model\Config $config
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Catalog\Model\Resource\Product $productResource
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Set\Collection $attrSetCollection
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Rule\Block\Editable $editable
     * @param \Magento\Catalog\Model\Product\Type $type
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Backend\Helper\Data $backendData,
        \Magento\Eav\Model\Config $config,
        \Magento\Catalog\Model\Product $product,
        \Magento\Catalog\Model\Resource\Product $productResource,
        \Magento\Eav\Model\Resource\Entity\Attribute\Set\Collection $attrSetCollection,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Rule\Block\Editable $editable,
        \Magento\Catalog\Model\Product\Type $type,
        array $data = array()
    ) {
        $this->_editable = $editable;
        $this->_type = $type;
        parent::__construct(
            $context,
            $backendData,
            $config,
            $product,
            $productResource,
            $attrSetCollection,
            $localeFormat,
            $data
        );
        $this->setType('Magento\TargetRule\Model\Actions\Condition\Product\Attributes');
        $this->setValue(null);
        $this->setValueType(self::VALUE_TYPE_SAME_AS);
    }

    /**
     * Add special action product attributes
     *
     * @param array &$attributes
     * @return void
     */
    protected function _addSpecialAttributes(array &$attributes)
    {
        parent::_addSpecialAttributes($attributes);
        $attributes['type_id'] = __('Type');
    }

    /**
     * Retrieve value by option
     * Rewrite for Retrieve options by Product Type attribute
     *
     * @param mixed $option
     * @return string
     */
    public function getValueOption($option = null)
    {
        if (!$this->getData('value_option') && $this->getAttribute() == 'type_id') {
            $this->setData('value_option', $this->_type->getAllOption());
        }
        return parent::getValueOption($option);
    }

    /**
     * Retrieve select option values
     * Rewrite Rewrite for Retrieve options by Product Type attribute
     *
     * @return array
     */
    public function getValueSelectOptions()
    {
        if (!$this->getData('value_select_options') && $this->getAttribute() == 'type_id') {
            $this->setData('value_select_options', $this->_type->getAllOption());
        }
        return parent::getValueSelectOptions();
    }

    /**
     * Retrieve input type
     * Rewrite for define input type for Product Type attribute
     *
     * @return string
     */
    public function getInputType()
    {
        $attributeCode = $this->getAttribute();
        if ($attributeCode == 'type_id') {
            return 'select';
        }
        return parent::getInputType();
    }

    /**
     * Retrieve value element type
     * Rewrite for define value element type for Product Type attribute
     *
     * @return string
     */
    public function getValueElementType()
    {
        $attributeCode = $this->getAttribute();
        if ($attributeCode == 'type_id') {
            return 'select';
        }
        return parent::getValueElementType();
    }

    /**
     * Retrieve model content as HTML
     * Rewrite for add value type chooser
     *
     * @return string
     */
    public function asHtml()
    {
        return __(
            'Product %1%2%3%4%5%6%7',
            $this->getTypeElementHtml(),
            $this->getAttributeElementHtml(),
            $this->getOperatorElementHtml(),
            $this->getValueTypeElementHtml(),
            $this->getValueElementHtml(),
            $this->getRemoveLinkHtml(),
            $this->getChooserContainerHtml()
        );
    }

    /**
     * Returns options for value type select box
     *
     * @return array
     */
    public function getValueTypeOptions()
    {
        $options = array(array('value' => self::VALUE_TYPE_CONSTANT, 'label' => __('Constant Value')));

        if ($this->getAttribute() == 'category_ids') {
            $options[] = array(
                'value' => self::VALUE_TYPE_SAME_AS,
                'label' => __('the Same as Matched Product Categories')
            );
            $options[] = array(
                'value' => self::VALUE_TYPE_CHILD_OF,
                'label' => __('the Child of the Matched Product Categories')
            );
        } else {
            $options[] = array(
                'value' => self::VALUE_TYPE_SAME_AS,
                'label' => __('Matched Product %1', $this->getAttributeName())
            );
        }

        return $options;
    }

    /**
     * Retrieve Value Type display name
     *
     * @return string
     */
    public function getValueTypeName()
    {
        $options = $this->getValueTypeOptions();
        foreach ($options as $option) {
            if ($option['value'] == $this->getValueType()) {
                return $option['label'];
            }
        }
        return '...';
    }

    /**
     * Retrieve Value Type Select Element
     *
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     */
    public function getValueTypeElement()
    {
        $elementId = $this->getPrefix() . '__' . $this->getId() . '__value_type';
        $element = $this->getForm()->addField(
            $elementId,
            'select',
            array(
                'name' => 'rule[' . $this->getPrefix() . '][' . $this->getId() . '][value_type]',
                'values' => $this->getValueTypeOptions(),
                'value' => $this->getValueType(),
                'value_name' => $this->getValueTypeName(),
                'class' => 'value-type-chooser'
            )
        )->setRenderer(
            $this->_editable
        );
        return $element;
    }

    /**
     * Retrieve value type element HTML code
     *
     * @return string
     */
    public function getValueTypeElementHtml()
    {
        $element = $this->getValueTypeElement();
        return $element->getHtml();
    }

    /**
     * Load attribute property from array
     *
     * @param array $array
     * @return $this
     */
    public function loadArray($array)
    {
        parent::loadArray($array);

        if (isset($array['value_type'])) {
            $this->setValueType($array['value_type']);
        }
        return $this;
    }

    /**
     * Retrieve condition data as array
     *
     * @param array $arrAttributes
     * @return array
     */
    public function asArray(array $arrAttributes = array())
    {
        $array = parent::asArray($arrAttributes);
        $array['value_type'] = $this->getValueType();
        return $array;
    }

    /**
     * Retrieve condition data as string
     *
     * @param string $format
     * @return string
     */
    public function asString($format = '')
    {
        if (!$format) {
            $format = ' %s %s %s %s';
        }
        return sprintf(
            __('Target Product ') . $format,
            $this->getAttributeName(),
            $this->getOperatorName(),
            $this->getValueTypeName(),
            $this->getValueName()
        );
    }

    /**
     * Retrieve SELECT WHERE condition for product collection
     *
     * @param \Magento\Catalog\Model\Resource\Product\Collection $collection
     * @param \Magento\TargetRule\Model\Index $object
     * @param array &$bind
     * @return \Zend_Db_Expr|false
     */
    public function getConditionForCollection($collection, $object, &$bind)
    {
        /* @var $resource \Magento\TargetRule\Model\Resource\Index */
        $attributeCode = $this->getAttribute();
        $valueType = $this->getValueType();
        $operator = $this->getOperator();
        $resource = $object->getResource();

        if ($attributeCode == 'category_ids') {
            $select = $object->select()->from(
                $resource->getTable('catalog_category_product'),
                'COUNT(*)'
            )->where(
                'product_id=e.entity_id'
            );
            if ($valueType == self::VALUE_TYPE_SAME_AS) {
                $operator = '!{}' == $operator ? '!()' : '()';
                $where = $resource->getOperatorBindCondition(
                    'category_id',
                    'category_ids',
                    $operator,
                    $bind,
                    array('bindArrayOfIds')
                );
                $select->where($where);
            } else if ($valueType == self::VALUE_TYPE_CHILD_OF) {
                $concatenated = $resource->getReadConnection()->getConcatSql(array('tp.path', "'/%'"));
                $subSelect = $resource->select()->from(
                    array('tc' => $resource->getTable('catalog_category_entity')),
                    'entity_id'
                )->join(
                    array('tp' => $resource->getTable('catalog_category_entity')),
                    "tc.path " . ($operator == '!()' ? 'NOT ' : '') . "LIKE {$concatenated}",
                    array()
                )->where(
                    $resource->getOperatorBindCondition(
                        'tp.entity_id',
                        'category_ids',
                        '()',
                        $bind,
                        array('bindArrayOfIds')
                    )
                );
                $select->where('category_id IN(?)', $subSelect);
            } else {
                //self::VALUE_TYPE_CONSTANT
                $operator = $operator == '==' ? '' : 'NOT';
                $value = $resource->bindArrayOfIds($this->getValue());
                $where = "category_id {$operator} IN(" . implode(',', $value) . ")";
                $select->where($where);
            }

            return new \Zend_Db_Expr(sprintf('(%s) > 0', $select->assemble()));
        }

        if ($valueType == self::VALUE_TYPE_CONSTANT) {
            $useBind = false;
            $value = $this->getValue();
            // split value by commas into array for operators with multiple operands
            if (($operator == '()' || $operator == '!()') && is_string($value) && trim($value) != '') {
                $value = preg_split('/\s*,\s*/', trim($value), -1, PREG_SPLIT_NO_EMPTY);
            }
        } else {
            //self::VALUE_TYPE_SAME_AS
            $useBind = true;
        }

        $attribute = $this->getAttributeObject();
        if (!$attribute) {
            return false;
        }

        if ($attribute->isStatic()) {
            $field = "e.{$attributeCode}";
            if ($useBind) {
                $where = $resource->getOperatorBindCondition($field, $attributeCode, $operator, $bind);
            } else {
                $where = $resource->getOperatorCondition($field, $operator, $value);
            }
            $where = sprintf('(%s)', $where);
        } elseif ($attribute->isScopeGlobal()) {
            $table = $attribute->getBackendTable();
            $select = $object->select()
                ->from(array('table' => $table), 'COUNT(*)')
                ->where('table.entity_id = e.entity_id')
                ->where('table.attribute_id=?', $attribute->getId())
                ->where('table.store_id=?', 0);
            if ($useBind) {
                $select->where($resource->getOperatorBindCondition('table.value', $attributeCode, $operator, $bind));
            } else {
                $select->where($resource->getOperatorCondition('table.value', $operator, $value));
            }

            $select = $resource->getReadConnection()->getIfNullSql($select);
            $where = sprintf('(%s) > 0', $select);
        } else {
            //scope store and website
            $valueExpr = $resource->getReadConnection()->getCheckSql(
                'attr_s.value_id > 0',
                'attr_s.value',
                'attr_d.value'
            );
            $table = $attribute->getBackendTable();
            $select = $object->select()->from(
                array('attr_d' => $table),
                'COUNT(*)'
            )->joinLeft(
                array('attr_s' => $table),
                $resource->getReadConnection()->quoteInto(
                    'attr_s.entity_id = attr_d.entity_id AND attr_s.attribute_id = attr_d.attribute_id' .
                    ' AND attr_s.store_id=?',
                    $object->getStoreId()
                ),
                array()
            )->where(
                'attr_d.entity_id = e.entity_id'
            )->where(
                'attr_d.attribute_id=?',
                $attribute->getId()
            )->where(
                'attr_d.store_id=?',
                0
            );
            if ($useBind) {
                $select->where($resource->getOperatorBindCondition($valueExpr, $attributeCode, $operator, $bind));
            } else {
                $select->where($resource->getOperatorCondition($valueExpr, $operator, $value));
            }

            $where = sprintf('(%s) > 0', $select);
        }
        return new \Zend_Db_Expr($where);
    }
}
