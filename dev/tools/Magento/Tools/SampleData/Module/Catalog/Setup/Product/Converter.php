<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\Catalog\Setup\Product;

class Converter
{
    /**
     * @var \Magento\Catalog\Service\V1\Category\Tree\ReadServiceInterface
     */
    protected $categoryReadService;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var \Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory
     */
    protected $attributeCollectionFactory;

    /**
     * @var \Magento\Eav\Model\Resource\Entity\Attribute\Option\CollectionFactory
     */
    protected $attrOptionCollectionFactory;

    /**
     * @var array
     */
    protected $attributeCodeOptionsPair;

    /**
     * @var array
     */
    protected $attributeCodeOptionValueIdsPair;

    /**
     * @var int
     */
    protected $attributeSetId;

    /**
     * @param \Magento\Catalog\Service\V1\Category\Tree\ReadServiceInterface $categoryReadService
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory $attributeCollectionFactory
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory
     */
    public function __construct(
        \Magento\Catalog\Service\V1\Category\Tree\ReadServiceInterface $categoryReadService,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory $attributeCollectionFactory,
        \Magento\Eav\Model\Resource\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory
    ) {
        $this->categoryReadService = $categoryReadService;
        $this->eavConfig = $eavConfig;
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        $this->attrOptionCollectionFactory = $attrOptionCollectionFactory;
    }

    /**
     * Convert CSV format row to array
     *
     * @param array $row
     * @return array
     */
    public function convertRow($row)
    {
        $data = [];
        foreach ($row as $field => $value) {
            if ('category' == $field) {
                $data['category_ids'] = $this->getCategoryIds($this->getArrayValue($value));
                continue;
            }

            if ('qty' == $field) {
                $data['quantity_and_stock_status'] = ['qty' => $value];
                continue;
            }

            $convertedField = $this->convertField($data, $field, $value);
            if ($convertedField) {
                continue;
            }

            $options = $this->getAttributeOptionValueIdsPair($field);
            if ($options) {
                $value = $this->setOptionsToValues($options, $value);
            }
            $data[$field] = $value;

        }
        return $data;
    }

    /**
     * Convert field
     *
     * @param array $data
     * @param string $field
     * @param string $value
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function convertField(&$data, $field, $value)
    {
        return false;
    }

    /**
     * Assign options data to attribute value
     *
     * @param mixed $options
     * @param array $values
     * @return array|mixed
     */
    protected function setOptionsToValues($options, $values)
    {
        $values = $this->getArrayValue($values);
        $result = [];
        foreach ($values as $value) {
            if (isset($options[$value])) {
                $result[] = $options[$value];
            }
        }
        return count($result) == 1 ? current($result) : $result;
    }

    /**
     * Get formatted array value
     *
     * @param mixed $value
     * @return array
     */
    protected function getArrayValue($value)
    {
        if (is_array($value)) {
            return $value;
        }
        if (false !== strpos($value, "\n")) {
            $value = array_filter(explode("\n", $value));
        }
        return !is_array($value) ? [$value] : $value;
    }

    /**
     * Get product category ids from array
     *
     * @param array $categories
     * @return array
     */
    protected function getCategoryIds($categories)
    {
        $ids = [];
        $tree = $this->categoryReadService->tree();
        foreach ($categories as $name) {
            foreach ($tree->getChildren() as $child) {
                if ($child->getName() == $name) {
                    $tree = $child;
                    $ids[] = $child->getId();
                    break;
                }
            }
        }
        return $ids;
    }

    /**
     * Get attribute options by attribute code
     *
     * @param string $attributeCode
     * @return \Magento\Eav\Model\Resource\Entity\Attribute\Option\Collection|null
     */
    public function getAttributeOptions($attributeCode)
    {
        if (!$this->attributeCodeOptionsPair) {
            $this->loadAttributeOptions();
        }
        return isset($this->attributeCodeOptionsPair[$attributeCode])
            ? $this->attributeCodeOptionsPair[$attributeCode]
            : null;
    }

    /**
     * Loads all attributes with options for current attribute set
     *
     * @return $this
     */
    protected function loadAttributeOptions()
    {
        /** @var \Magento\Catalog\Model\Resource\Product\Attribute\Collection $collection */
        $collection = $this->attributeCollectionFactory->create();
        $collection->addFieldToSelect(array('attribute_code', 'attribute_id'));
        $collection->setAttributeSetFilter($this->getAttributeSetId());
        $collection->setFrontendInputTypeFilter(array('in' => array('select', 'multiselect')));
        foreach ($collection as $item) {
            $options = $this->attrOptionCollectionFactory->create()
                ->setAttributeFilter($item->getAttributeId())->setPositionOrder('asc', true)->load();
            $this->attributeCodeOptionsPair[$item->getAttributeCode()] = $options;
        }
        return $this;
    }

    /**
     * Find attribute option value pair
     *
     * @param mixed $attributeCode
     * @return mixed
     */
    protected function getAttributeOptionValueIdsPair($attributeCode)
    {
        if (!empty($this->attributeCodeOptionValueIdsPair[$attributeCode])) {
            return $this->attributeCodeOptionValueIdsPair[$attributeCode];
        }

        $options = $this->getAttributeOptions($attributeCode);
        $opt = [];
        if ($options) {
            foreach ($options as $option) {
                $opt[$option->getValue()] = $option->getId();
            }
        }
        $this->attributeCodeOptionValueIdsPair[$attributeCode] = $opt;
        return $this->attributeCodeOptionValueIdsPair[$attributeCode];
    }

    /**
     * @return int
     */
    protected function getAttributeSetId()
    {
        return $this->attributeSetId;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setAttributeSetId($value)
    {
        if ($this->attributeSetId != $value) {
            $this->loadAttributeOptions();
        }
        $this->attributeSetId = $value;
        return $this;
    }
}
