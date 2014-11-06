<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\ProductOptions;

class TypeList implements \Magento\Catalog\Api\ProductCustomOptionOptionTypeListInterface
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var \Magento\Catalog\Api\Data\ProductCustomOptionOptionTypeDataBuilder
     */
    protected $builder;

    /**
     * @param Config $config
     * @param \Magento\Catalog\Api\Data\ProductCustomOptionOptionTypeDataBuilder $builder
     */
    public function __construct(
        Config $config,
        \Magento\Catalog\Api\Data\ProductCustomOptionOptionTypeDataBuilder $builder
    ) {
        $this->config = $config;
        $this->builder = $builder;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        $output = [];
        foreach ($this->config->getAll() as $option) {
            foreach ($option['types'] as $type) {
                if ($type['disabled']) {
                    continue;
                }
                $itemData = [
                    'label' => __($type['label']),
                    'code' => $type['name'],
                    'group' => __($option['label'])
                ];
                $output[] = $this->builder->populateWithArray($itemData)->create();
            }
        }
        return $output;
    }
}
