<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1\Product\Option;

use Magento\Bundle\Model\Product\Type;
use Magento\Bundle\Service\V1\Data\Product\Option;
use Magento\Bundle\Service\V1\Data\Product\OptionConverter;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManager;
use Magento\Webapi\Exception;

class WriteService implements WriteServiceInterface
{
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var Type
     */
    private $type;
    /**
     * @var \Magento\Bundle\Service\V1\Data\Product\OptionConverter
     */
    private $optionConverter;
    /**
     * @var \Magento\Store\Model\StoreManager
     */
    private $storeManager;

    /**
     * @param ProductRepository $productRepository
     * @param Type $type
     * @param OptionConverter $optionConverter
     * @param StoreManager $storeManager
     */
    public function __construct(
        ProductRepository $productRepository,
        Type $type,
        OptionConverter $optionConverter,
        StoreManager $storeManager
    ) {
        $this->productRepository = $productRepository;
        $this->type = $type;
        $this->optionConverter = $optionConverter;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($productSku, $optionId)
    {
        $product = $this->getProduct($productSku);
        $optionCollection = $this->type->getOptionsCollection($product);

        /** @var \Magento\Bundle\Model\Option $removeOption */
        $removeOption = null;
        /** @var \Magento\Bundle\Model\Option $option */
        foreach ($optionCollection as $option) {
            if ($option->getId() == $optionId) {
                $removeOption = $option;
            }
        }
        if ($removeOption === null) {
            throw new NoSuchEntityException('Requested option doesn\'t exist');
        }
        $removeOption->delete();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function add($productSku, Option $option)
    {
        $product = $this->getProduct($productSku);
        $optionModel = $this->optionConverter->createModelFromData($option, $product);
        $optionModel->setStoreId($this->storeManager->getStore()->getId());

        try {
            $optionModel->save();
        } catch (\Exception $e) {
            throw new CouldNotSaveException('Could not save option');
        }

        return $this->optionConverter->createDataFromModel($optionModel, $product);
    }

    /**
     * @param string $productSku
     * @return Product
     * @throws Exception
     */
    private function getProduct($productSku)
    {
        $product = $this->productRepository->get($productSku);

        if ($product->getTypeId() != \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
            throw new Exception('Only implemented for bundle product', Exception::HTTP_FORBIDDEN);
        }

        return $product;
    }
}
