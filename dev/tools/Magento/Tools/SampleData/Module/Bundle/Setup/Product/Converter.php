<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\SampleData\Module\Bundle\Setup\Product;

/**
 * Convert data for bundle product
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
     * Convert CSV format row to array
     *
     * @param array $row
     * @return array
     */
    public function convertRow($row)
    {
        $data = parent::convertRow($row);
        if (!empty($row['bundle_options'])) {
            $bundleData = $this->convertBundleOptions($row['bundle_options']);
            $data = array_merge($data, $bundleData);
        }
        return $data;
    }

    /**
     * @inheritdoc
     */
    protected function convertField(&$data, $field, $value)
    {
        return $field == 'bundle_options';
    }

    /**
     * Convert bundle options
     *
     * @param array $bundleOptionsData
     * @return array
     */
    protected function convertBundleOptions($bundleOptionsData)
    {
        $resultOptions = [];
        $resultSelections = [];
        $bundleOptions = explode("\n", $bundleOptionsData);
        $optionPosition = 1;
        foreach ($bundleOptions as $option) {
            if (strpos($option, ':') === false) {
                continue;
            }
            $optionData = explode(':', $option);
            if (empty($optionData[0]) || empty($optionData[1])) {
                continue;
            }
            $optionType = 'select';
            $optionName = $optionData[0];
            if (strpos($optionName, '|') !== false) {
                $optionNameData = explode(':', $optionName);
                $optionName = $optionNameData[0];
                $optionType = $optionNameData[1];
            }
            $resultOptions[] = array(
                'title' => $optionName,
                'option_id' => '',
                'delete' => '',
                'type' => $optionType,
                'required' => '1',
                'position' => $optionPosition++,
            );
            $skuList = explode(',', $optionData[1]);
            $selections = [];
            $selectionPosition = 1;
            foreach ($skuList as $sku) {
                $productId = $this->getProductIdBySku($sku);
                if (!$productId) {
                    continue;
                }
                $selections[] = array(
                    'selection_id' => '',
                    'option_id' => '',
                    'product_id' => $productId,
                    'delete' => '',
                    'selection_price_value' => '0.00',
                    'selection_price_type' => '0',
                    'selection_qty' => '1',
                    'selection_can_change_qty' => '1',
                    'position' => $selectionPosition++,
                );
            }
            $resultSelections[] = $selections;
        }
        return array('bundle_options_data' => $resultOptions, 'bundle_selections_data' => $resultSelections);
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
