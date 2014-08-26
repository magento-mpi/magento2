<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Service\V1\Product;

use Magento\Bundle\Service\V1\Data\Product\Link;
use Magento\Bundle\Service\V1\Data\Product\Option;
use Magento\Bundle\Service\V1\Product\Link\ReadService as LinkReadService;
use Magento\Bundle\Service\V1\Product\Link\WriteService as LinkWriteService;
use Magento\Bundle\Service\V1\Product\Option\ReadService as OptionReadService;
use Magento\Bundle\Service\V1\Product\Option\WriteService as OptionWriteService;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\Catalog\Service\V1\Data\Product;
use Magento\Catalog\Service\V1\Product\ProductSaveProcessorInterface;

/**
 * Class to save bundle products
 */
class BundleProductSaveProcessor implements ProductSaveProcessorInterface
{
    /**
     * @var LinkWriteService
     */
    private $linkWriteService;

    /**
     * @var OptionWriteService
     */
    private $optionWriteService;

    /**
     * @var LinkReadService
     */
    private $linkReadService;

    /**
     * @var OptionReadService
     */
    private $optionReadService;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * Initialize dependencies.
     *
     * @param LinkWriteService   $linkWriteService
     * @param OptionWriteService $optionWriteService
     * @param LinkReadService    $linkReadService
     * @param OptionReadService  $optionReadService
     * @param ProductRepository  $productRepository
     */
    public function __construct(
        LinkWriteService $linkWriteService,
        OptionWriteService $optionWriteService,
        LinkReadService $linkReadService,
        OptionReadService $optionReadService,
        ProductRepository $productRepository
    ) {
        $this->linkWriteService = $linkWriteService;
        $this->optionWriteService = $optionWriteService;
        $this->linkReadService = $linkReadService;
        $this->optionReadService = $optionReadService;
        $this->productRepository = $productRepository;
    }

    /**
     * Process bundle-related attributes of product during its creation.
     *
     * @param ProductModel $product
     * @param Product      $productData
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return string
     */
    public function create(ProductModel $product, Product $productData)
    {

        if ($productData->getTypeId() != ProductType::TYPE_BUNDLE) {
            return null;
        }

        /**
         * @var string $productSku
         */
        $productSku = $productData->getSku();

        /**
         * @var Link[] $bundleProductLinks
         */
        $bundleProductLinks = $productData->getCustomAttribute('bundle_product_links');
        if (is_array($bundleProductLinks)) {
            foreach ($bundleProductLinks as $link) {
                $this->linkWriteService->addChild($productSku, $link);
            }
        }

        /**
         * @var Option[] $bundleProductOptions
         */
        $bundleProductOptions = $productData->getCustomAttribute('bundle_product_options');
        if (is_array($bundleProductOptions)) {
            foreach ($bundleProductOptions as $option) {
                $this->optionWriteService->add($productSku, $option);
            }
        }

        return $productSku;
    }

    /**
     * Update bundle-related attributes of product.
     *
     * @param string $id
     * @param Product $updatedProduct
     * @return string
     */
    public function update($id, Product $updatedProduct)
    {
        /**
         * @var Product $existingProduct
         */
        $existingProduct = $this->productRepository->get($id, true);
        if ($existingProduct->getTypeId() != ProductType::TYPE_BUNDLE) {
            return null;
        }

        /**
         * @var string $productSku
         */
        $productSku = $existingProduct->getSku();

        /**
         * @var Link[] $existingProductLinks
         */
        $existingProductLinks = $this->linkReadService->getChildren($id);
        /**
         * @var Link[] $newProductLinks
         */
        $newProductLinks = $updatedProduct->getCustomAttribute('bundle_product_links');
        /**
         * @var Link[] $linksToDelete
         */
        $linksToDelete = array_udiff($existingProductLinks, $newProductLinks, array($this, 'compareLinks'));
        foreach ($linksToDelete as $link) {
            $this->linkWriteService->removeChild($productSku, $link->getOptionId(), $link->getSku());
        }
        /**
         * @var Link[] $linksToAdd
         */
        $linksToAdd = array_udiff($newProductLinks, $existingProductLinks, array($this, 'compareLinks'));
        foreach ($linksToAdd as $link) {
            $this->linkWriteService->addChild($productSku, $link);
        }

        /**
         * @var Option[] $existingProductOptions
         */
        $existingProductOptions = $this->optionReadService->getList($productSku);
        /**
         * @var Option[] $newProductOptions
         */
        $newProductOptions = $updatedProduct->getCustomAttribute('bundle_product_options');
        /**
         * @var Option[] $optionsToDelete
         */
        $optionsToDelete = array_udiff($existingProductOptions, $newProductOptions, array($this, 'compareOptions'));
        foreach ($optionsToDelete as $option) {
            $this->optionWriteService->remove($productSku, $option->getId());
        }
        /**
         * @var Option[] $optionsToAdd
         */
        $optionsToAdd = array_udiff($newProductOptions, $existingProductOptions, array($this, 'compareOptions'));
        foreach ($optionsToAdd as $option) {
            $this->optionWriteService->add($productSku, $option);
        }

        return $productSku;
    }

    /**
     * Delete bundle-related attributes of product.
     *
     * @param Product $product
     * @return void
     */
    public function delete(Product $product)
    {
        if ($product->getTypeId() != ProductType::TYPE_BUNDLE) {
            return;
        }

        /**
         * @var string $productSku
         */
        $productSku = $product->getSku();

        /**
         * @var Link[] $bundleProductLinks
         */
        $bundleProductLinks = $product->getCustomAttribute('bundle_product_links');
        foreach ($bundleProductLinks as $link) {
            $this->linkWriteService->removeChild($productSku, $link->getOptionId(), $link->getSku());
        }

        /**
         * @var Option[] $bundleProductOptions
         */
        $bundleProductOptions = $product->getCustomAttribute('bundle_product_options');
        foreach ($bundleProductOptions as $option) {
            $this->optionWriteService->remove($productSku, $option->getId());
        }

        return;
    }

    /**
     * Compare two links to determine if they are equal
     *
     * @param Link $firstLink
     * @param Link $secondLink
     * @return int
     */
    private function compareLinks(Link $firstLink, Link $secondLink)
    {
        if ($firstLink->getSku() === $secondLink->getSku()) {
            return 0;
        } else {
            return 1;
        }
    }

    /**
     * Compare two options and determine if they are equal
     *
     * @param Option $firstOption
     * @param Option $secondOption
     * @return int
     */
    private function compareOptions(Option $firstOption, Option $secondOption)
    {
        if ($firstOption->getId() == $secondOption->getId()) {
            return 0;
        } else {
            return 1;
        }
    }
}
