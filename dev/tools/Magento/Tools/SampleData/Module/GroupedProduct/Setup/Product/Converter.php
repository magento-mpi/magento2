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
class Converter extends \Magento\Tools\SampleData\Module\Catalog\Setup\Product\Converter
{
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
        parent::__construct(
            $categoryReadService,
            $eavConfig,
            $attributeCollectionFactory,
            $attrOptionCollectionFactory
        );
        $this->productCollection = $productCollection->addAttributeToSelect('sku');
    }

    /**
     * @inheritdoc
     */
    protected function convertField(&$data, $field, $value)
    {
        if ('associated_sku' == $field) {
            $data['grouped_link_data'] = $this->convertGroupedAssociated($value);
            return true;
        }
        return false;
    }

    /**
     * @param string $associated
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
}
