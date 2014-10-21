<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\ProductOptions;

use \Magento\Catalog\Api\Data\ProductCustomOptionOptionTypeInterface as OptionType;

class TypeList implements \Magento\Catalog\Api\ProductCustomOptionOptionTypeListInterface
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var \Magento\Catalog\Api\Data\ProductCustomOptionOptionInterfaceBuilder
     */
    protected $builder;

    /**
     * @param Config $config
     * @param \Magento\Catalog\Api\Data\ProductCustomOptionOptionInterfaceBuilder $builder
     */
    public function __construct(
        Config $config,
        \Magento\Catalog\Api\Data\ProductCustomOptionOptionTypeInterfaceBuilder $builder
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
                    OptionType::LABEL => __($type['label']),
                    OptionType::CODE => $type['name'],
                    OptionType::GROUP => __($option['label'])
                ];
                $output[] = $this->builder->populateWithArray($itemData)->create();
            }
        }
        return $output;
    }
}
