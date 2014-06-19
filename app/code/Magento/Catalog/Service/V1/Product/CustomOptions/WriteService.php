<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions;

use Magento\Framework\Exception\CouldNotSaveException;

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
     * @param Data\Option\Converter $optionConverter
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     */
    public function __construct(
        Data\Option\Converter $optionConverter,
        \Magento\Catalog\Model\ProductRepository $productRepository
    ) {
        $this->optionConverter = $optionConverter;
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function add($productSku, \Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option $option)
    {
        $product = $this->productRepository->get($productSku);
        $optionData = $this->optionConverter->covert($option);

        $product->setCanSaveCustomOptions(true);
        $product->setProductOptions([$optionData]);

        try {
            $product->save();
        } catch (\Exception $e) {
            throw new CouldNotSaveException('Could not save group price');
        }

        return true;
    }
}
