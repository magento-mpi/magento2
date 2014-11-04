<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Product\Option;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

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
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Catalog\Model\Resource\Product\Option $optionResource
     */
    public function __construct(
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Catalog\Model\Resource\Product\Option $optionResource
    ) {
        $this->productRepository = $productRepository;
        $this->optionResource = $optionResource;
    }

    /**
     * {@inheritdoc}
     */
    public function getList($productSku)
    {
        $product = $this->productRepository->get($productSku, true);
        return $product->getOptions();
    }

    /**
     * {@inheritdoc}
     */
    public function get($productSku, $optionId)
    {
        $product = $this->productRepository->get($productSku);
        $option = $product->getOptionById($optionId);
        if (is_null($option)) {
            throw NoSuchEntityException::singleField('optionId', $optionId);
        }
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
        $productSku = $option->getProductSku();
        $product = $this->productRepository->get($productSku, true);
        $optionData = $option->getData();
        $values = $option->getData('values');
        $valuesData = [];
        if (!empty($values)) {
            foreach ($values as $key => $value) {
                $valuesData[$key] = $value->getData();
            }
        }
        $optionData['values'] = $valuesData;
        if ($option->getOptionId()) {
            if (!$product->getOptionById($option->getOptionId())) {
                throw new NoSuchEntityException();
            }
            $originalValues = $product->getOptionById($option->getOptionId())->getValues();
            if (!empty($optionData['values'])) {
                $optionData['values'] = $this->markRemovedValues($optionData['values'], $originalValues);
            }
        }

        unset($optionData['product_sku']);

        $product->setProductOptions([$optionData]);
        $existingOptions = $product->getOptions();
        try {
            $this->productRepository->save($product, true);
        } catch (\Exception $e) {
            throw new CouldNotSaveException('Could not save product option');
        }

        $product = $this->productRepository->get($productSku, true);
        if (!$option->getOptionId()) {
            $currentOptions = $product->getOptions();

            $newID = array_diff(array_keys($currentOptions), array_keys($existingOptions));
            if (empty($newID)) {
                throw new CouldNotSaveException('Could not save product option');
            }
            $newID = current($newID);
        } else {
            $newID = $option->getOptionId();
        }
        $option = $this->get($productSku, $newID);
        return $option;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByIdentifier($productSku, $optionId)
    {
        $product = $this->productRepository->get($productSku);
        $options = $this->getList($productSku);
        $option = $this->get($productSku, $optionId);
        if (!$option->getId() || !isset($options[$option->getId()])) {
            throw NoSuchEntityException::singleField('optionId', $optionId);
        }
        unset($options[$optionId]);
        try {
            $this->delete($option);
            if (empty($options)) {
                $this->productRepository->save($product);
            }
        } catch (\Exception $e) {
            throw new CouldNotSaveException('Could not remove custom option');
        }
        return true;
    }

    /**
     * Mark original values for removal if they are absent among new values
     *
     * @param $newValues array
     * @param $originalValues \Magento\Catalog\Model\Product\Option\Value[]
     * @return array
     */
    protected function markRemovedValues($newValues, $originalValues)
    {
        $idsToLeave = [];

        foreach ($newValues as $newValue) {
            if (array_key_exists('option_type_id', $newValue)) {
                $idsToLeave[] = $newValue['option_type_id'];
            }
        }
        /** @var $originalValue \Magento\Catalog\Model\Product\Option\Value */
        foreach ($originalValues as $originalValue) {
            if (!in_array($originalValue->getData('option_type_id'), $idsToLeave)) {
                $originalValue->setData('is_delete', 1);
                $newValues[] = $originalValue->getData();
            }
        }

        return $newValues;
    }
}
