<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions\OptionValue;

use \Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionValueBuilder,
    \Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionValue,
    \Magento\Framework\Exception\NoSuchEntityException;

class ReadService implements ReadServiceInterface
{
    /**
     * @var OptionValueBuilder
     */
    protected $optionValueBuilder;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * @var \Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionValue\ReaderInterface
     */
    protected $optionReader;

    /**
     * @param OptionValueBuilder $optionValueBuilder
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param OptionValue\ReaderInterface $optionReader
     */
    public function __construct(
        OptionValueBuilder $optionValueBuilder,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionValue\ReaderInterface $optionReader
    ) {
        $this->optionValueBuilder = $optionValueBuilder;
        $this->productRepository = $productRepository;
        $this->optionReader = $optionReader;
    }

    /**
     * {@inheritdoc}
     */
    public function getList($productSku, $optionId)
    {
        $product = $this->productRepository->get($productSku);
        $option = $product->getOptionById($optionId);

        if (!$option || !$option->getId()) {
            throw NoSuchEntityException::singleField('optionId', $optionId);
        }

        return $this->optionReader->read($option);
    }
}
