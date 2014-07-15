<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Service\V1\Product\Link;

use \Magento\Catalog\Model\ProductRepository;
use \Magento\ConfigurableProduct\Model\Resource\Product\Type\ConfigurableFactory;

class WriteService implements WriteServiceInterface
{
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * @var \Magento\ConfigurableProduct\Model\Resource\Product\Type\ConfigurableFactory
     */
    protected $typeConfigurableFactory;

    /**
     * @param ProductRepository $productRepository
     * @param ConfigurableFactory $typeConfigurableFactory
     */
    public function __construct(
        ProductRepository $productRepository,
        ConfigurableFactory $typeConfigurableFactory
    ) {
        $this->productRepository = $productRepository;
        $this->typeConfigurableFactory = $typeConfigurableFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function addChild($productSku, $childSku)
    {
        $product = $this->productRepository->get($productSku);
        $configurableType = $this->typeConfigurableFactory->create();

        $productIds = [$childSku];
        if (is_array($productIds)) {
            $configurableType->saveProducts($product, $productIds);
        }
        return true;
    }

}