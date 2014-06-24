<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionBuilder;
use \Magento\Catalog\Model\Product\Price\ScopeResolver as PriceScopeResolver;

class WriteService implements WriteServiceInterface
{
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * @var Data\Option\Converter
     */
    protected $optionConverter;

    /**
     * @var Data\OptionValue\ReaderInterface
     */
    protected $optionValueReader;

    /**
     * @var OptionBuilder
     */
    protected $optionBuilder;

    /**
     * @param OptionBuilder $optionBuilder
     * @param Data\Option\Converter $optionConverter
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param Data\OptionValue\ReaderInterface $optionValueReader
     */
    public function __construct(
        OptionBuilder $optionBuilder,
        Data\Option\Converter $optionConverter,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        Data\OptionValue\ReaderInterface $optionValueReader
    ) {
        $this->optionBuilder = $optionBuilder;
        $this->optionConverter = $optionConverter;
        $this->productRepository = $productRepository;
        $this->optionValueReader = $optionValueReader;
    }

    /**
     * {@inheritdoc}
     */
    public function add($productSku, \Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option $option)
    {
        $product = $this->productRepository->get($productSku);
        $optionData = $this->optionConverter->convert($option);

        $product->setCanSaveCustomOptions(true);
        $product->setProductOptions([$optionData]);

        try {
            $product->save();
        } catch (\Exception $e) {
            throw new CouldNotSaveException('Could not save product option');
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($productSku, $optionId)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->productRepository->get($productSku);

        $options = $product->getOptions();

        if (empty($options) || is_null($product->getOptionById($optionId))) {
            throw NoSuchEntityException::singleField('optionId', $optionId);
        }

        $customOptions = array();
        foreach ($options as $option) {
            $data= array(
                Data\Option::OPTION_ID => $option->getId(),
                Data\Option::TITLE => $option->getTitle(),
                Data\Option::TYPE => $option->getType(),
                Data\Option::IS_REQUIRE => $option->getIsRequire(),
                Data\Option::SORT_ORDER => $option->getSortOrder(),
                Data\Option::VALUE => $this->optionValueReader->read($option)
            );
            $optionDataObject = $this->optionBuilder->populateWithArray($data)->create();
            $customOptions[$option->getId()] = $this->optionConverter->convert($optionDataObject);

        }

        $product->setCanSaveCustomOptions(true);

        $customOptions[$optionId]['is_delete'] = '1';
        $product->setProductOptions($customOptions);
        $product->setHasOptions(true);
        try {
            $product->save();
        } catch (\Exception $e) {
            throw new CouldNotSaveException('Could not remove custom option');
        }

        return true;
    }

    /**
     * @param \Magento\Catalog\Model\Product\Option\Value[] $allOptionValues
     * @param array $valuesToLeave
     * @return array
     */
    protected function markValuesForRemoval($allOptionValues, $valuesToLeave)
    {
        $markedValues = $valuesToLeave;
        $idsToLeave = [];
        foreach ($valuesToLeave as $valueData) {
            $idsToLeave[] = $valueData['option_type_id'];
        }

        /** @var $optionValue \Magento\Catalog\Model\Product\Option\Value */
        foreach ($allOptionValues as $optionValue) {
            if (!in_array($optionValue->getData('option_type_id'), $idsToLeave)) {
                $optionValueData = $optionValue->getData();
                $optionValueData['is_delete'] = 1;
                $markedValues[] = $optionValueData;
            }
        }
        return $markedValues;
    }

    /**
     * {@inheritdoc}
     */
    public function update(
        $productSku,
        $optionId,
        \Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option $option
    ) {
        $product = $this->productRepository->get($productSku);
        $optionData = $this->optionConverter->convert($option);
        if (!$product->getOptionById($optionId)) {
            throw new NoSuchEntityException();
        }
        $optionData['option_id'] = $optionId;
        $originalValues = $product->getOptionById($optionId)->getValues();
        if (!empty($originalValues) && count($optionData['values']) < count($originalValues)) {
            $optionData['values'] = $this->markValuesForRemoval($originalValues, $optionData['values']);
        }

        $product->setCanSaveCustomOptions(true);
        $product->setProductOptions([$optionData]);

        try {
            $product->save();
        } catch (\Exception $e) {
            throw new CouldNotSaveException('Could not save custom option');
        }

        return true;
    }
}
