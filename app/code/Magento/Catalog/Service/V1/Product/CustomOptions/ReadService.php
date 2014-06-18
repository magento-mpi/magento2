<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions;

use Magento\Framework\Exception\NoSuchEntityException;

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
     * @var Data\OptionBuilder
     */
    protected $optionBuilder;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * @var Data\OptionValue\ReaderInterface
     */
    protected $optionValueReader;

    /**
     * @param \Magento\Catalog\Model\ProductOptions\ConfigInterface $productOptionConfig
     * @param Data\OptionTypeBuilder $optionTypeBuilder
     * @param Data\OptionBuilder $optionBuilder
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param Data\OptionValue\ReaderInterface $optionValueReader
     */
    public function __construct(
        \Magento\Catalog\Model\ProductOptions\ConfigInterface $productOptionConfig,
        Data\OptionTypeBuilder $optionTypeBuilder,
        Data\OptionBuilder $optionBuilder,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        Data\OptionValue\ReaderInterface $optionValueReader
    ) {
        $this->productOptionConfig = $productOptionConfig;
        $this->optionTypeBuilder = $optionTypeBuilder;
        $this->optionBuilder = $optionBuilder;
        $this->productRepository = $productRepository;
        $this->optionValueReader = $optionValueReader;
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
                    Data\OptionType::GROUP => __($option['group'])
                ];
                $output[] = $this->optionTypeBuilder->populateWithArray($itemData)->create();
            }
        }
        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function getList($productSku)
    {
        $product = $this->productRepository->get($productSku);
        $output = [];
        /** @var $option \Magento\Catalog\Model\Product\Option */
        foreach ($product->getProductOptionsCollection() as $option) {
            $output[] = $this->_createOptionDataObject($option);
        }
        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function get($productSku, $optionId)
    {
        $product = $this->productRepository->get($productSku);
        $option = $product->getOptionById($optionId);
        if ($option === null) {
            throw new NoSuchEntityException();
        }
        return $this->_createOptionDataObject($option);
    }

    /**
     * Create option data object
     *
     * @param \Magento\Catalog\Model\Product\Option $option
     * @return \Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option array
     */
    protected function _createOptionDataObject(\Magento\Catalog\Model\Product\Option $option)
    {
        $data = array(
            Data\Option::OPTION_ID => $option->getId(),
            Data\Option::TITLE => $option->getTitle(),
            Data\Option::TYPE => $option->getType(),
            Data\Option::IS_REQUIRE => $option->getIsRequire(),
            Data\Option::SORT_ORDER => $option->getSortOrder(),
            Data\Option::VALUE => $this->optionValueReader->read($option)
        );
        return $this->optionBuilder->populateWithArray($data)->create();
    }
}
