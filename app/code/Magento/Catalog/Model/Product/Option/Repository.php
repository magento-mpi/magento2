<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Product\Option;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata;
use Magento\Framework\Exception\InputException;

class Repository implements \Magento\Catalog\Api\ProductCustomOptionRepositoryInterface
{
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * @var \Magento\Catalog\Model\Resource\Product\Option
     */
    protected $optionResource;

    /**
     * @var Converter
     */
    protected $optionConverter;

    /**
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Catalog\Model\Resource\Product\Option $optionResource
     * @param Converter $optionConverter
     */
    public function __construct(
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Catalog\Model\Resource\Product\Option $optionResource,
        Converter $optionConverter
    ) {
        $this->productRepository = $productRepository;
        $this->optionResource = $optionResource;
        $this->optionConverter = $optionConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function getList($productSku)
    {
        $product = $this->productRepository->get($productSku);
        return $product->getOptions();
    }

    /**
     * {@inheritdoc}
     */
    public function get($productSku, $optionId)
    {
        $product = $this->productRepository->get($productSku);
        $option = $product->getOptionById($optionId);
        return $option;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(\Magento\Catalog\Api\Data\ProductCustomOptionOptionInterface $entity)
    {
        $this->optionResource->delete($entity);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Magento\Catalog\Api\Data\ProductCustomOptionOptionInterface $option)
    {
        $product = $this->productRepository->get($option->getProductSKU());
        if ($option->getOptionId()) {
            throw new InputException('Unable to save option. Please, check input data.');
        }
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
}
