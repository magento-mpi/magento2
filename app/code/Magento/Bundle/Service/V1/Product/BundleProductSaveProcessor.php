<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Service\V1\Product;

use Magento\Bundle\Service\V1\Product\Link\ReadService as LinkReadService;
use Magento\Bundle\Service\V1\Product\Link\WriteService as LinkWriteService;
use Magento\Bundle\Service\V1\Product\Option\ReadService as OptionReadService;
use Magento\Bundle\Service\V1\Product\Option\WriteService as OptionWriteService;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\Product\Type as ProductType;

/**
 * Class to save bundle products
 */
class BundleProductSaveProcessor implements \Magento\Catalog\Service\V1\Product\ProductSaveProcessorInterface
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
     * Create bundle.
     *
     * @param \Magento\Catalog\Model\Product           $product
     * @param \Magento\Catalog\Service\V1\Data\Product $productData
     *
     * @return bool
     */
    public function create(
        \Magento\Catalog\Model\Product $product,
        \Magento\Catalog\Service\V1\Data\Product $productData
    ) {
        /**
         * @var String $productSku
         */
        $productSku = $productData->getSku();

        /**
         * @var \Magento\Bundle\Service\V1\Data\Product\Link\Metadata[] $bundleProductLinks
         */
        $bundleProductLinks = $productData->getCustomAttribute('bundle_product_links');
        foreach ($bundleProductLinks as $link) {
            $this->linkWriteService->addChild($productSku, $link);
        }

        /**
         * @var \Magento\Bundle\Service\V1\Data\Product\Option[] $bundleProductOptions
         */
        $bundleProductOptions = $productData->getCustomAttribute('bundle_product_options');
        foreach ($bundleProductOptions as $option) {
            $this->optionWriteService->add($productSku, $option);
        }

        return true;
    }

    /**
     * Update bundle.
     *
     * @param string                                   $id
     * @param \Magento\Catalog\Service\V1\Data\Product $updatedProduct
     *
     * @return bool
     */
    public function update($id, \Magento\Catalog\Service\V1\Data\Product $updatedProduct)
    {
        /**
         * @var \Magento\Catalog\Service\V1\Data\Product $existingProduct
         */
        $existingProduct = $this->productRepository->getById($id, true);
        if ($existingProduct->getTypeId() != ProductType::TYPE_BUNDLE) {
            return true;
        }

        /**
         * @var String $productSku
         */
        $productSku = $existingProduct->getSku();
        /*
         * TODO: Expecting that MAGETWO-27274 will refactor the removeChild method in Link/WriteService
         * and the option Id will no longer be required.
         */
        $optionId = 'dummy';

        /**
         * @var \Magento\Bundle\Service\V1\Data\Product\Link\Metadata[] $existingProductLinks
         */
        $existingProductLinks = $this->linkReadService->getChildren($id);
        /**
         * @var \Magento\Bundle\Service\V1\Data\Product\Link\Metadata[] $newProductLinks
         */
        $newProductLinks = $updatedProduct->getCustomAttribute('bundle_product_links');
        /**
         * @var \Magento\Bundle\Service\V1\Data\Product\Link\Metadata[] $linksToDelete
         */
        $linksToDelete = array_udiff($existingProductLinks, $newProductLinks, array($this, '_compareLinks'));
        foreach ($linksToDelete as $link) {
            $this->linkWriteService->removeChild($productSku, $optionId, $link->getSku());
        }
        /**
         * @var \Magento\Bundle\Service\V1\Data\Product\Link\Metadata[] $linksToAdd
         */
        $linksToAdd = array_udiff($newProductLinks, $existingProductLinks, array($this, '_compareLinks'));
        foreach ($linksToAdd as $link) {
            $this->linkWriteService->addChild($productSku, $link);
        }

        /**
         * @var \Magento\Bundle\Service\V1\Data\Product\Option[] $existingProductOptions
         */
        $existingProductOptions = $this->optionReadService->getList($productSku);
        /**
         * @var \Magento\Bundle\Service\V1\Data\Product\Option[] $newProductOptions
         */
        $newProductOptions = $updatedProduct->getCustomAttribute('bundle_product_options');
        /**
         * @var \Magento\Bundle\Service\V1\Data\Product\Option[] $optionsToDelete
         */
        $optionsToDelete = array_udiff($existingProductOptions, $newProductOptions, array($this, '_compareOptions'));
        foreach ($optionsToDelete as $option) {
            $this->optionWriteService->remove($productSku, $option->getId());
        }
        /**
         * @var \Magento\Bundle\Service\V1\Data\Product\Option[] $optionsToAdd
         */
        $optionsToAdd = array_udiff($newProductOptions, $existingProductOptions, array($this, '_compareOptions'));
        foreach ($optionsToAdd as $option) {
            $this->optionWriteService->add($productSku, $option);
        }

        return true;
    }

    /**
     * Delete bundle.
     *
     * @param \Magento\Catalog\Service\V1\Data\Product $product
     *
     * @return bool
     */
    public function delete(\Magento\Catalog\Service\V1\Data\Product $product)
    {
        /**
         * @var String $productSku
         */
        $productSku = $product->getSku();
        /*
         * TODO: Expecting that MAGETWO-27274 will refactor the removeChild method in Link/WriteService
         * and the option Id will no longer be required.
         */
        $optionId = 'dummy';

        /**
         * @var \Magento\Bundle\Service\V1\Data\Product\Link\Metadata[] $bundleProductLinks
         */
        $bundleProductLinks = $product->getCustomAttribute('bundle_product_links');
        foreach ($bundleProductLinks as $link) {
            $this->linkWriteService->removeChild($productSku, $optionId, $link->getSku());
        }

        /**
         * @var \Magento\Bundle\Service\V1\Data\Product\Option[] $bundleProductOptions
         */
        $bundleProductOptions = $product->getCustomAttribute('bundle_product_options');
        foreach ($bundleProductOptions as $option) {
            $this->optionWriteService->remove($productSku, $option->getId());
        }

        return true;
    }

    /**
     * Compare two links to determine if they are equal
     * @param \Magento\Bundle\Service\V1\Data\Product\Link\Metadata $link1
     * @param \Magento\Bundle\Service\V1\Data\Product\Link\Metadata $link2
     * @return int
     */
    private function _compareLinks(
        \Magento\Bundle\Service\V1\Data\Product\Link\Metadata $link1,
        \Magento\Bundle\Service\V1\Data\Product\Link\Metadata $link2
    ) {
        if ($link1->getSku() === $link2->getSku()) {
            return 0;
        } else {
            return 1;
        }
    }

    /**
     * Compare two options and determine if they are equal
     * @param \Magento\Bundle\Service\V1\Data\Product\Option $option1
     * @param \Magento\Bundle\Service\V1\Data\Product\Option $option2
     * @return int
     */
    private function _compareOptions(
        \Magento\Bundle\Service\V1\Data\Product\Option $option1,
        \Magento\Bundle\Service\V1\Data\Product\Option $option2
    ) {
        if ($option1->getId() == $option2->getId()) {
            return 0;
        } else {
            return 1;
        }
    }
}
