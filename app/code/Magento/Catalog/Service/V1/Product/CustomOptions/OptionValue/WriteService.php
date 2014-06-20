<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions\OptionValue;

use \Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionBuilder,
    \Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionValue,
    \Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option,
    \Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Converter as OptionConverter,
    \Magento\Framework\Exception\NoSuchEntityException,
    \Magento\Framework\Exception\CouldNotSaveException,
    \Magento\Framework\Exception\InputException;

class WriteService implements WriteServiceInterface
{
    /**
     * @var OptionBuilder
     */
    protected $optionBuilder;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * @var OptionValue\ReaderInterface
     */
    protected $optionReader;

    /**
     * Product Option Config
     *
     * @var \Magento\Catalog\Model\ProductOptions\ConfigInterface
     */
    protected $productOptionConfig;

    /**
     * @var OptionConverter
     */
    protected $optionConverter;

    /**
     * @param OptionBuilder $optionBuilder
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param OptionValue\ReaderInterface $optionReader
     * @param \Magento\Catalog\Model\ProductOptions\ConfigInterface $productOptionConfig
     * @param OptionConverter $optionConverter
     */
    public function __construct(
        OptionBuilder $optionBuilder,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        OptionValue\ReaderInterface $optionReader,
        \Magento\Catalog\Model\ProductOptions\ConfigInterface $productOptionConfig,
        OptionConverter $optionConverter
    ) {
        $this->productOptionConfig = $productOptionConfig;
        $this->optionBuilder = $optionBuilder;
        $this->productRepository = $productRepository;
        $this->optionReader = $optionReader;
        $this->optionConverter = $optionConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function add($productSku, $optionId, OptionValue $value)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->productRepository->get($productSku);

        $options = $product->getOptions();

        $option = $product->getOptionById($optionId);
        if (is_null($option)) {
            throw NoSuchEntityException::singleField('optionId', $optionId);
        }

        $config = $this->productOptionConfig->getOption('select');
        $allowedTypes = [];
        foreach ($config['types'] as $type) {
            if ($type['disabled']) {
                continue;
            }
            $allowedTypes[] = $type['name'];
        }

        if (!in_array($option->getType(), $allowedTypes)) {
            throw new InputException('Adding option value to option type ' . $option->getType() . ' is not supported');
        }

        $customOptions = array();
        /** @var $option \Magento\Catalog\Model\Product\Option */
        foreach ($options as $option) {
            $data = [
                Option::OPTION_ID => $option->getId(),
                Option::TITLE => $option->getTitle(),
                Option::TYPE => $option->getType(),
                Option::IS_REQUIRE => $option->getIsRequire(),
                Option::SORT_ORDER => $option->getSortOrder(),
                Option::VALUE => $this->optionReader->read($option)
            ];

            if ($optionId == $option->getId()) {
                $data[Option::VALUE][] = $value;
            }
            $optionDataObject = $this->optionBuilder->populateWithArray($data)->create();
            $optionData = $this->optionConverter->covert($optionDataObject);
            $customOptions[$option->getId()] = $optionData;
        }

        $product->setCanSaveCustomOptions(true);
        $product->setProductOptions($customOptions);
        $product->setHasOptions(true);
        try {
            $product->save();
        } catch (\Exception $e) {
            throw new CouldNotSaveException('Could not add custom option value');
        }

        return true;
    }
}
