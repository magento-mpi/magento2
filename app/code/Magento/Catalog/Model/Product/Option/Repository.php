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
     * Get the list of custom options for a specific product
     *
     * @param string $productSku
     * @return \Magento\Catalog\Api\Data\ProductCustomOptionOptionInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     *
     * @see \Magento\Catalog\Service\V1\Product\CustomOptions\ReadServiceInterface::getList - previous implementation
     */
    public function getList($productSku)
    {
        $product = $this->productRepository->get($productSku);
        return $product->getOptions();
    }

    /**
     * Get custom option for a specific product
     *
     * @param string $productSku
     * @param string $optionId
     * @return \Magento\Catalog\Api\Data\ProductCustomOptionOptionInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     *
     * @see \Magento\Catalog\Service\V1\Product\CustomOptions\ReadServiceInterface::get - previous implementation
     */
    public function get($productSku, $optionId)
    {
        $product = $this->productRepository->get($productSku);
        $option = $product->getOptionById($optionId);
        return $option;
    }

    /**
     * Remove custom option from product
     *
     * @param string $productSku
     * @param int $optionId
     * @throws \Magento\Framework\Exception\NoSuchEntityException|\Magento\Framework\Exception\CouldNotSaveException
     * @return bool
     *
     * @see \Magento\Catalog\Service\V1\Product\CustomOptions\WriteServiceInterface::remove - previous implementation
     */
    public function delete(\Magento\Catalog\Api\Data\ProductCustomOptionOptionInterface $entity)
    {
        $this->optionResource->delete($entity);
        return true;
    }

    /**
     * Add custom option to the product
     *
     * @param string $productSku
     * @param \Magento\Catalog\Api\Data\ProductCustomOptionOptionInterface $option
     * @return \Magento\Catalog\Api\Data\ProductCustomOptionOptionInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     *
     * @see \Magento\Catalog\Service\V1\Product\CustomOptions\WriteServiceInterface::add - previous implementation
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
