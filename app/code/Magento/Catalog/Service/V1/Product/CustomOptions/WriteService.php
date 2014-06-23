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
}
