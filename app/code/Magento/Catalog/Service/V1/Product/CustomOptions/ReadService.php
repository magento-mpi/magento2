<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions;

class ReadService implements \Magento\Catalog\Service\V1\Product\CustomOptions\ReadServiceInterface
{
    /**
     * Product Option Config
     *
     * @var \Magento\Catalog\Model\ProductOptions\ConfigInterface
     */
    protected $productOptionConfig;

    /**
     * @var Data\OptionTypeBuilder
     */
    protected $optionTypeBuilder;

    /**
     * @param \Magento\Catalog\Model\ProductOptions\ConfigInterface $productOptionConfig
     * @param Data\OptionTypeBuilder $optionTypeBuilder
     */
    public function __construct(
        \Magento\Catalog\Model\ProductOptions\ConfigInterface $productOptionConfig,
        Data\OptionTypeBuilder $optionTypeBuilder
    ) {
        $this->productOptionConfig = $productOptionConfig;
        $this->optionTypeBuilder = $optionTypeBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypes()
    {
        $output = [];
        foreach ($this->productOptionConfig->getAll() as $option) {
            foreach ($option['types'] as $type) {
                if ($type['disabled']) {
                    continue;
                }
                $itemData = [
                    Data\OptionType::LABEL => __($type['label']),
                    Data\OptionType::CODE => $type['name'],
                    Data\OptionType::GROUP => __($option['label'])
                ];
                $output[] = $this->optionTypeBuilder->populateWithArray($itemData)->create();
            }
        }
        return $output;
    }
}
