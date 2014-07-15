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
use Magento\Framework\Exception\StateException;

class WriteService implements WriteServiceInterface
{
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * @var ConfigurableFactory
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
        /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableType */
        $configurableType = $this->typeConfigurableFactory->create();

        $child = $this->productRepository->get($childSku);

        $childrenIds = $configurableType->getChildrenIds($product->getId());
        if (in_array($child->getId(), $childrenIds)) {

        }

        $childrenIds[] = $child->getId();
        $configurableType->saveProducts($product, $childrenIds);
        return true;
    }
}