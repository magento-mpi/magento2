<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * CatalogWidget Rule Product Condition data model
 */
namespace Magento\CatalogWidget\Model\Rule\Condition;

/**
 * Class Product
 */
class Product extends \Magento\Rule\Model\Condition\Product\AbstractProduct
{
    /**
     * {@inheritdoc}
     */
    protected $elementName = 'parameters';

    /**
     * @var array
     */
    protected $joinedAttributes = array();

    /**
     * Store manager
     *
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Backend\Helper\Data $backendData
     * @param \Magento\Eav\Model\Config $config
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Resource\Product $productResource
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Set\Collection $attrSetCollection
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Backend\Helper\Data $backendData,
        \Magento\Eav\Model\Config $config,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Resource\Product $productResource,
        \Magento\Eav\Model\Resource\Entity\Attribute\Set\Collection $attrSetCollection,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Framework\StoreManagerInterface $storeManager,
        array $data = array()
    ) {
        $this->storeManager = $storeManager;
        parent::__construct(
            $context,
            $backendData,
            $config,
            $productFactory,
            $productResource,
            $attrSetCollection,
            $localeFormat,
            $data
        );
    }

    /**
     * {@inheritdoc}
     */
    public function loadAttributeOptions()
    {
        $productAttributes = $this->_productResource->loadAllAttributes()->getAttributesByCode();

        $attributes = array();
        foreach ($productAttributes as $attribute) {
            if (!$attribute->getFrontendLabel() || $attribute->getFrontendInput() == 'text') {
                continue;
            }
            $attributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
        }

        $this->_addSpecialAttributes($attributes);

        asort($attributes);
        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _addSpecialAttributes(array &$attributes)
    {
        parent::_addSpecialAttributes($attributes);
        $attributes['sku'] = __('SKU');
    }

    /**
     * Add condition to collection
     *
     * @param \Magento\Catalog\Model\Resource\Product\Collection $collection
     * @return $this
     */
    public function addToCollection($collection)
    {
        $attribute = $this->getAttributeObject();
        if ('category_ids' == $attribute->getAttributeCode() || $attribute->isStatic()) {
            return $this;
        }

        if ($attribute->getBackend() && $attribute->isScopeGlobal()) {
            $this->addGlobalAttribute($attribute, $collection);
        } else {
            $this->addNotGlobalAttribute($attribute, $collection);
        }

        $attributes = $this->getRule()->getCollectedAttributes();
        $attributes[$attribute->getAttributeCode()] = true;
        $this->getRule()->setCollectedAttributes($attributes);

        return $this;
    }

    /**
     * @param \Magento\Catalog\Model\Resource\Eav\Attribute $attribute
     * @param \Magento\Catalog\Model\Resource\Product\Collection $collection
     * @return $this
     */
    protected function addGlobalAttribute(
        \Magento\Catalog\Model\Resource\Eav\Attribute $attribute,
        \Magento\Catalog\Model\Resource\Product\Collection $collection
    ) {
        $storeId =  $this->storeManager->getStore()->getId();

        switch ($attribute->getBackendType()) {
            case 'decimal':
            case 'datetime':
            case 'int':
                $alias = 'at_' . $attribute->getAttributeCode();
                $collection->addAttributeToSelect($attribute->getAttributeCode(), 'inner');
                break;
            default:
                $alias = 'at_'. md5($this->getId()) . $attribute->getAttributeCode();
                $collection->getSelect()->join(
                    array($alias => $collection->getTable('catalog_product_index_eav')),
                    "($alias.entity_id = e.entity_id) AND ($alias.store_id = $storeId)" .
                    " AND ($alias.attribute_id = {$attribute->getId()})",
                    array()
                );
        }

        $this->joinedAttributes[$attribute->getAttributeCode()] = $alias;

        return $this;
    }

    /**
     * @param \Magento\Catalog\Model\Resource\Eav\Attribute $attribute
     * @param \Magento\Catalog\Model\Resource\Product\Collection $collection
     * @return $this
     */
    protected function addNotGlobalAttribute(
        \Magento\Catalog\Model\Resource\Eav\Attribute $attribute,
        \Magento\Catalog\Model\Resource\Product\Collection $collection
    ) {
        $storeId =  $this->storeManager->getStore()->getId();
        $values = $collection->getAllAttributeValues($attribute);
        $validEntities = array();
        if ($values) {
            foreach ($values as $entityId => $storeValues) {
                if (isset($storeValues[$storeId])) {
                    if ($this->validateAttribute($storeValues[$storeId])) {
                        $validEntities[] = $entityId;
                    }
                } else {
                    if ($this->validateAttribute($storeValues[\Magento\Store\Model\Store::DEFAULT_STORE_ID])) {
                        $validEntities[] = $entityId;
                    }
                }
            }
        }
        $this->setOperator('()');
        $this->unsetData('value_parsed');
        if ($validEntities) {
            $this->setData('value', implode(',', $validEntities));
        } else {
            $this->unsetData('value');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMappedSqlField()
    {
        $result = '';
        if ($this->getAttributeObject()->isScopeGlobal()) {
            if (isset($this->joinedAttributes[$this->getAttribute()])) {
                $result = $this->joinedAttributes[$this->getAttribute()] . '.value';
            } else {
                $result = parent::getMappedSqlField();
            }
        } elseif ($this->getValueParsed()) {
            $result = 'e.entity_id';
        }

        return $result;
    }
}
