<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1\Product\Link;

use Magento\Bundle\Model\Option;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Webapi\Exception;

class WriteService implements WriteServiceInterface
{
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    private $productRepository;

    /**
     * @var \Magento\Bundle\Model\Resource\BundleFactory
     */
    protected $bundleFactory;

    /**
     * @param ProductRepository $productRepository
     * @param \Magento\Bundle\Model\Resource\BundleFactory $bundleFactory
     */
    public function __construct(
        ProductRepository $productRepository,
        \Magento\Bundle\Model\Resource\BundleFactory $bundleFactory
    ) {
        $this->productRepository = $productRepository;
        $this->bundleFactory = $bundleFactory;
    }

    /**
     * @inheritdoc
     */
    public function removeChild($productSku, $optionId, $childSku)
    {
        $product = $this->productRepository->get($productSku);

        if ($product->getTypeId() != \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
            throw new Exception('Bundle product SKU is expected', Exception::HTTP_FORBIDDEN);
        }

        $excludeSelectionIds = array();
        $usedProductIds = array();
        $removeSelectionIds = array();
        foreach ($this->getOptions($product) as $option) {
            foreach ($option->getSelections() as $selection) {
                if (($selection->getSku() == $childSku) && ($selection->getOptionId() == $optionId)) {
                    $removeSelectionIds[] = $selection->getSelectionId();
                    continue;
                }
                $excludeSelectionIds[] = $selection->getSelectionId();
                $usedProductIds[] = $selection->getProductId();
            }
        }
        if (empty($removeSelectionIds)) {
            throw new NoSuchEntityException('Requested bundle option product doesn\'t exist');
        }
        /* @var $resource \Magento\Bundle\Model\Resource\Bundle */
        $resource = $this->bundleFactory->create();
        $resource->dropAllUnneededSelections($product->getId(), $excludeSelectionIds);
        $resource->saveProductRelations($product->getId(), array_unique($usedProductIds));

        return true;
    }

    /**
     * @param Product $product
     * @return Option[]
     */
    private function getOptions(Product $product)
    {
        $product->getTypeInstance()->setStoreFilter($product->getStoreId(), $product);

        $optionCollection = $product->getTypeInstance()->getOptionsCollection($product);

        $selectionCollection = $product->getTypeInstance()->getSelectionsCollection(
            $product->getTypeInstance()->getOptionsIds($product),
            $product
        );

        return $optionCollection->appendSelections($selectionCollection);
    }
}
