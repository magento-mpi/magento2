<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Service\V1\Product;

use Magento\Bundle\Model\Option;
use Magento\Bundle\Service\V1\Product\Option\ReadService as OptionReadService;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Service\V1\Product\ProductLoadProcessorInterface;

/**
 * Add bundle product attributes to products during load.
 */
class BundleProductLoadProcessor implements ProductLoadProcessorInterface
{
    /**
     * @var OptionReadService
     */
    private $optionReadService;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @param OptionReadService $optionReadService
     * @param ProductRepository $productRepository
     */
    public function __construct(
        OptionReadService $optionReadService,
        ProductRepository $productRepository
    ) {
        $this->optionReadService = $optionReadService;
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function load($id, \Magento\Catalog\Service\V1\Data\ProductBuilder $productBuilder)
    {
        /** @var \Magento\Catalog\Model\Product */
        $product = $this->productRepository->get($id);
        if ($product->getTypeId() != ProductType::TYPE_BUNDLE) {
            return;
        }

        $productBuilder->setCustomAttribute(
            'bundle_product_options',
            $this->optionReadService->getList($product->getSku())
        );
    }
}
