<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reminder\Model\Rule\Condition\Wishlist;

/**
 * Product attribute value condition
 */
class Attributes
    extends \Magento\Rule\Model\Condition\Product\AbstractProduct
{
    /**
     * Config
     *
     * @var \Magento\Eav\Model\Config
     */
    protected $_config;

    /**
     * Rule Resource
     *
     * @var \Magento\Reminder\Model\Resource\Rule
     */
    protected $_ruleResource;

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Backend\Helper\Data $backendData
     * @param \Magento\Eav\Model\Config $config
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Catalog\Model\Resource\Product $productResource
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Set\Collection $attrSetCollection
     * @param \Magento\Reminder\Model\Resource\Rule $ruleResource
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Backend\Helper\Data $backendData,
        \Magento\Eav\Model\Config $config,
        \Magento\Catalog\Model\Product $product,
        \Magento\Catalog\Model\Resource\Product $productResource,
        \Magento\Eav\Model\Resource\Entity\Attribute\Set\Collection $attrSetCollection,
        \Magento\Reminder\Model\Resource\Rule $ruleResource,
        array $data = array()
    ) {
        parent::__construct($context, $backendData, $config, $product, $productResource, $attrSetCollection, $data);
        $this->setType('Magento\Reminder\Model\Rule\Condition\Wishlist\Attributes');
        $this->setValue(null);
        $this->_config = $config;
        $this->_ruleResource = $ruleResource;
    }

    /**
     * Customize default operator input by type mapper for some types
     *
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
            'label' => __('Product Attributes')
        );
    }

    /**
     * Get HTML of condition string
     *
     * @return string
     */
    public function asHtml()
    {
        return __('Product %1', strtolower(parent::asHtml()));
    }

    /**
     * Get product attribute object
     *
     * @return \Magento\Catalog\Model\Resource\Eav\Attribute
     */
    public function getAttributeObject()
    {
        return $this->_config->getAttribute('catalog_product', $this->getAttribute());
    }

    /**
     * Get resource
     *
     * @return \Magento\Reminder\Model\Resource\Rule
     */
    public function getResource()
    {
        return $this->_ruleResource;
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
     * @param int|\Zend_Db_Expr $website
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
