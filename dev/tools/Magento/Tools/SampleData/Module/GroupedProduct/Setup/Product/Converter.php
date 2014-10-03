<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\SampleData\Module\GroupedProduct\Setup\Product;

/**
 * Convert data for grouped product
 */
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
     * @var \Magento\Catalog\Model\Resource\Product\Collection
     */
    protected $productCollection;

    /**
     * @var array
     */
    protected $productIds;

    /**
     * @param \Magento\Catalog\Service\V1\Category\Tree\ReadServiceInterface $categoryReadService
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory $attributeCollectionFactory
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory
     * @param \Magento\Catalog\Model\Resource\Product\Collection $productCollection
     */
    public function __construct(
        \Magento\Catalog\Service\V1\Category\Tree\ReadServiceInterface $categoryReadService,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory $attributeCollectionFactory,
        \Magento\Eav\Model\Resource\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        \Magento\Catalog\Model\Resource\Product\Collection $productCollection
    ) {
        $this->categoryReadService = $categoryReadService;
        $this->eavConfig = $eavConfig;
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        $this->attrOptionCollectionFactory = $attrOptionCollectionFactory;
        $this->productCollection = $productCollection->addAttributeToSelect('sku');
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
            if ('associated_sku' == $field) {
                $data['grouped_link_data'] = $this->convertGroupedAssociated($value);
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
                $value = count($result) == 1 ? current($result) : $result;
            }
            $data[$field] = $value;
        }
        return $data;
    }

    /**
     * @param str $associated
     * @return array
     */
    public function convertGroupedAssociated($associated)
    {
        $skuList = explode(',', $associated);
        $data = [];
        $position = 0;
        foreach ($skuList as $sku) {
            $productId = $this->getProductIdBySku($sku);
            if (!$productId) {
                continue;
            }
            $data[$productId] = array(
                'id' => $productId,
                'position' => $position++,
                'qty' => '0',
            );
        }
        return $data;
    }

    /**
     * Retrieve product ID by sku
     *
     * @param string $sku
     * @return int|null
     */
    protected function getProductIdBySku($sku)
    {
        if (empty($this->productIds)) {
            foreach ($this->productCollection as $product) {
                $this->productIds[$product->getSku()] = $product->getId();
            }
        }
        if (isset($this->productIds[$sku])) {
            return $this->productIds[$sku];
        }
        return null;
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
     * @param str $attributeCode
     * @return null
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
     * Find attribute option value pair
     *
     * @param str $attributeCode
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
     * @param int $attributeSetId
     * @return $this
     */
    public function setAttributeSetId($attributeSetId)
    {
        $this->attributeSetId = $attributeSetId;
        return $this;
    }
}
