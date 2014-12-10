<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CustomerSegment\Model\Segment\Condition\Product;

use Zend_Db_Expr;

/**
 * Product attribute value condition
 */
class Attributes extends \Magento\Rule\Model\Condition\Product\AbstractProduct
{
    /**
     * Used for rule property field
     *
     * @var string
     */
    protected $_isUsedForRuleProperty = 'is_used_for_promo_rules';

    /**
     * @var \Magento\CustomerSegment\Model\Resource\Segment
     */
    protected $_resourceSegment;

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Backend\Helper\Data $backendData
     * @param \Magento\Eav\Model\Config $config
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Model\Resource\Product $productResource
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Set\Collection $attrSetCollection
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\CustomerSegment\Model\Resource\Segment $resourceSegment
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Backend\Helper\Data $backendData,
        \Magento\Eav\Model\Config $config,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\Resource\Product $productResource,
        \Magento\Eav\Model\Resource\Entity\Attribute\Set\Collection $attrSetCollection,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\CustomerSegment\Model\Resource\Segment $resourceSegment,
        array $data = []
    ) {
        $this->_resourceSegment = $resourceSegment;
        parent::__construct(
            $context,
            $backendData,
            $config,
            $productFactory,
            $productRepository,
            $productResource,
            $attrSetCollection,
            $localeFormat,
            $data
        );
        $this->setType('Magento\CustomerSegment\Model\Segment\Condition\Product\Attributes');
        $this->setValue(null);
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
            $this->_defaultOperatorInputByType['numeric'] = ['==', '!=', '>=', '>', '<=', '<'];
            $this->_defaultOperatorInputByType['string'] = ['==', '!=', '{}', '!{}'];
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
        if ($this->getAttributeObject()->getAttributeCode() == 'category_ids') {
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
        $conditions = [];
        foreach ($attributes as $code => $label) {
            $conditions[] = ['value' => $this->getType() . '|' . $code, 'label' => $label];
        }

        return ['value' => $conditions, 'label' => __('Product Attributes')];
    }

    /**
     * Get HTML of condition string
     *
     * @return string
     */
    public function asHtml()
    {
        return __('Product %1', parent::asHtml());
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
     * @return \Magento\CustomerSegment\Model\Resource\Segment
     */
    public function getResource()
    {
        return $this->_resourceSegment;
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
     * @param int|Zend_Db_Expr $website
     * @return string
     */
    public function getSubfilterSql($fieldName, $requireValid, $website)
    {
        $attribute = $this->getAttributeObject();
        $table = $attribute->getBackendTable();

        $resource = $this->getResource();
        $select = $resource->createSelect();
        $select->from(['main' => $table], ['entity_id']);

        if ($attribute->getAttributeCode() == 'category_ids') {
            $condition = $resource->createConditionSql(
                'cat.category_id',
                $this->getOperator(),
                $this->getValueParsed()
            );
            $categorySelect = $resource->createSelect();
            $categorySelect->from(
                ['cat' => $resource->getTable('catalog_category_product')],
                'product_id'
            )->where(
                $condition
            );
            $condition = 'main.entity_id IN (' . $categorySelect . ')';
        } elseif ($attribute->isStatic()) {
            $condition = $this->getResource()->createConditionSql(
                "main.{$attribute->getAttributeCode()}",
                $this->getOperator(),
                $this->getValue()
            );
        } else {
            $select->where('main.attribute_id = ?', $attribute->getId());
            $select->join(
                ['store' => $this->getResource()->getTable('store')],
                'main.store_id=store.store_id',
                []
            )->where(
                'store.website_id IN(?)',
                [0, $website]
            );
            $condition = $this->getResource()->createConditionSql(
                'main.value',
                $this->getOperator(),
                $this->getValue()
            );
        }
        $select->where($condition);
        $select->where('main.entity_id = ' . $fieldName);
        $inOperator = $requireValid ? 'EXISTS' : 'NOT EXISTS';
        if ($this->getCombineProductCondition()) {
            // when used as a child of History or List condition - "EXISTS" always set to "EXISTS"
            $inOperator = 'EXISTS';
        }
        return sprintf("%s (%s)", $inOperator, $select);
    }
}
