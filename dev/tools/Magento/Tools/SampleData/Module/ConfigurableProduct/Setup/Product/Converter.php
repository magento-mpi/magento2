<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tools\SampleData\Module\ConfigurableProduct\Setup\Product;

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
     * @var \Magento\ConfigurableProduct\Model\Product\Type\VariationMatrix
     */
    protected $variationMatrix;

    /**
     * @var array
     */
    protected $attributeCodeOptionsPair;

    /*
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
     * @param \Magento\ConfigurableProduct\Model\Product\Type\VariationMatrix $variationMatrix
     */
    public function __construct(
        \Magento\Catalog\Service\V1\Category\Tree\ReadServiceInterface $categoryReadService,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory $attributeCollectionFactory,
        \Magento\Eav\Model\Resource\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        \Magento\ConfigurableProduct\Model\Product\Type\VariationMatrix $variationMatrix
    ) {
        $this->categoryReadService = $categoryReadService;
        $this->eavConfig = $eavConfig;
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        $this->attrOptionCollectionFactory = $attrOptionCollectionFactory;
        $this->variationMatrix = $variationMatrix;
    }

    /**
     * @param array $row
     * @return array
     */
    public function convertRow($row)
    {
        $data = [];
        $configurableAttributes = [];
        foreach ($row as $field => $value) {
            if ('category' == $field) {
                $data['category_ids'] = $this->getCategoryIds($this->getArrayValue($value));
                continue;
            }
            if (in_array($field, array('color', 'size_general', 'size_pants'))) {
                if ($value) {
                    $configurableAttributes[$field] = $this->getArrayValue($value);
                }
                continue;
            }

            $options = $this->getAttributeOptionValueIdsPair($field);
            if ($options) {
                $value = $this->getArrayValue($value);
                $result = [];
                foreach ($value as $v) {
                    if (isset($options[$v])) {
                        $result[] = $options[$v];
                    }
                }
                $value = (count($result) == 1) ? current($result) : $result;
            }
            $data[$field] = $value;
        }
        if ($configurableAttributes) {
            $data['configurable_attributes_data'] = $this->convertAttributesData($configurableAttributes);
            $data['variations_matrix'] = $this->getVariationsMatrix($data);
            $data['new_variations_attribute_set_id'] = $this->getAttributeSetId();
        }
        return $data;
    }

    /**
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
     * @param array $configurableAttributes
     * @return array
     */
    protected function convertAttributesData($configurableAttributes)
    {
        $attributesData = [];
        foreach ($configurableAttributes as $attributeCode => $values) {
            $attribute = $this->eavConfig->getAttribute('catalog_product', $attributeCode);
            if (!$attribute->getId()) {
                continue;
            }
            $options = $this->getAttributeOptions($attribute->getAttributeCode());
            $attributeValues = [];
            $attributeOptions = [];
            foreach ($options as $option) {
                $attributeValues[] = array(
                    'value_index' => $option->getId(),
                    'is_percent' => false,
                    'pricing_value' => '',
                    'include' => (int)in_array($option->getValue(), $values)
                );
                $attributeOptions[] = array(
                    'value' => $option->getId(),
                    'label' => $option->getValue()
                );
            }
            $attributesData[$attribute->getId()] = array(
                'id' => '',
                'label' => $attribute->getFrontend()->getLabel(),
                'use_default' => '',
                'position' => '',
                'attribute_id' => $attribute->getId(),
                'attribute_code' => $attribute->getAttributeCode(),
                'code' => $attribute->getAttributeCode(),
                'values' => $attributeValues,
                'options' => $attributeOptions,
            );
        }
        return $attributesData;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function getVariationsMatrix($data)
    {
        $variations = $this->variationMatrix->getVariations($data['configurable_attributes_data']);
        $result = [];
        $productPrice = 100;
        $productName = $data['name'];
        $productSku = $data['sku'];
        foreach ($variations as $variation) {
            $attributeValues = array();
            $attributeLabels = array();
            $price = $productPrice;
            foreach ($data['configurable_attributes_data'] as $attributeData) {
                $attributeValues[$attributeData['attribute_code']] = $variation[$attributeData['attribute_id']]['value'];
                $attributeLabels[$attributeData['attribute_code']] = $variation[$attributeData['attribute_id']]['label'];
                if (isset($variation[$attributeData['attribute_id']]['price'])) {
                    $priceInfo = $variation[$attributeData['attribute_id']]['price'];
                    $price += ($priceInfo['is_percent'] ? $productPrice / 100.0 : 1.0) * $priceInfo['pricing_value'];
                }
            }
            $key = implode('-', $attributeValues);
            $result[$key] = [
                'image' => '',
                'name'   => $productName . '-' . implode('-', $attributeLabels),
                'sku'    => $productSku . '-' . implode('-', $attributeLabels),
                'configurable_attribute' => \json_encode($attributeValues),
                'quantity_and_stock_status' => ['qty' => '10'],
                'weight' => '1',
            ];
        }
        return $result;
    }

    /**
     * @param string $attributeCode
     * @return \Magento\Eav\Model\Resource\Entity\Attribute\Option\Collection|null
     */
    public function getAttributeOptions($attributeCode)
    {
        if (isset($this->attributeCodeOptionsPair[$attributeCode])) {
            return $this->attributeCodeOptionsPair[$attributeCode];
        }

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
        return null;
    }

    /**
     * @param string $attributeCode
     * @return mixed
     */
    protected function getAttributeOptionValueIdsPair($attributeCode)
    {
        if (isset($this->attributeCodeOptionValueIdsPair[$attributeCode])) {
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
        $this->attributeSetId = $value;
        return $this;
    }
}
