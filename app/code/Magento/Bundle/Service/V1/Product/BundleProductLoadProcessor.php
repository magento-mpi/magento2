<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1\Product;

use Magento\Bundle\Model\Option;
use Magento\Bundle\Service\V1\Product\Link\ReadService as LinkReadService;
use Magento\Bundle\Service\V1\Product\Option\ReadService as OptionReadService;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Service\V1\Product\ProductLoadProcessorInterface;

class BundleProductLoadProcessor implements ProductLoadProcessorInterface
{
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
     * @param OptionReadService $optionReadService
     * @param LinkReadService $linkReadService
     * @param ProductRepository $productRepository
     */
    public function __construct(
        OptionReadService $optionReadService,
        LinkReadService $linkReadService,
        ProductRepository $productRepository
    ) {
        $this->optionReadService = $optionReadService;
        $this->linkReadService = $linkReadService;
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function load(
        $id,
        \Magento\Catalog\Service\V1\Data\ProductBuilder $productBuilder
    ) {
        $product = $this->productRepository->getById($id);
        if ($product->getTypeId() != ProductType::TYPE_BUNDLE) {
            return true;
        }

        $productBuilder->setCustomAttribute(
            'bundle_product_options',
            $this->optionReadService->getList($product->getSku())
        );
        $productBuilder->setCustomAttribute(
            'bundle_product_links',
            $this->linkReadService->getChildren($id)
        );

        return true;
    }
}
