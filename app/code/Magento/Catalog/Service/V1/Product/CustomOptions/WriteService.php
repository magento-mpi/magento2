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
     * @var \Magento\Catalog\Model\Product\OptionFactory
     */
    protected $optionFactory;

    /**
     * @param OptionBuilder $optionBuilder
     * @param Data\Option\Converter $optionConverter
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param Data\OptionValue\ReaderInterface $optionValueReader
     * @param \Magento\Catalog\Model\Product\OptionFactory $optionFactory
     */
    public function __construct(
        OptionBuilder $optionBuilder,
        Data\Option\Converter $optionConverter,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        Data\OptionValue\ReaderInterface $optionValueReader,
        \Magento\Catalog\Model\Product\OptionFactory $optionFactory
    ) {
        $this->optionBuilder = $optionBuilder;
        $this->optionConverter = $optionConverter;
        $this->productRepository = $productRepository;
        $this->optionValueReader = $optionValueReader;
        $this->optionFactory = $optionFactory;
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

        $existingOptions = $product->getOptions();

        try {
            $product->save();
        } catch (\Exception $e) {
            throw new CouldNotSaveException('Could not save product option');
        }

        $productId = $product->getId();
        $product->reset();
        $product->load($productId);
        $currentOptions = $product->getOptions();

        $newID = array_diff(array_keys($currentOptions), array_keys($existingOptions));
        if (empty($newID)) {
            throw new CouldNotSaveException('Could not save product option');
        }
        $newID = current($newID);
        /** @var \Magento\Catalog\Model\Product\Option $newOption */
        $newOption = $currentOptions[$newID];
        $data= array(
            Data\Option::OPTION_ID => $newOption->getId(),
            Data\Option::TITLE => $newOption->getTitle(),
            Data\Option::TYPE => $newOption->getType(),
            Data\Option::IS_REQUIRE => $newOption->getIsRequire(),
            Data\Option::SORT_ORDER => $newOption->getSortOrder(),
            Data\Option::VALUE => $this->optionValueReader->read($newOption)
        );
        $optionDataObject = $this->optionBuilder->populateWithArray($data)->create();
        return $optionDataObject;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($productSku, $optionId)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->productRepository->get($productSku);
        $options = $product->getOptions();
        $option = $this->optionFactory->create();
        $option->load($optionId);
        if (!$option->getId() || !isset($options[$option->getId()])) {
            throw NoSuchEntityException::singleField('optionId', $optionId);
        }

        unset($options[$optionId]);
        try {
            $option->delete();
            if (empty($options)) {
                $product->setHasOptions(false);
                $product->save();
            }
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
