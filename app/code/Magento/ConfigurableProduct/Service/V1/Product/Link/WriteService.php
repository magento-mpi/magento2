<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Service\V1\Product\Link;

use \Magento\Catalog\Model\ProductRepository;
use \Magento\ConfigurableProduct\Model\Resource\Product\Type\Configurable;
use Magento\Framework\Exception\StateException;

class WriteService implements WriteServiceInterface
{
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * @var Configurable
     */
    protected $configurableType;

    /**
     * @param ProductRepository $productRepository
     * @param Configurable $configurableType
     * @internal param ConfigurableFactory $typeConfigurableFactory
     */
    public function __construct(
        ProductRepository $productRepository,
        Configurable $configurableType
    ) {
        $this->productRepository = $productRepository;
        $this->configurableType = $configurableType;
    }

    /**
     * {@inheritdoc}
     */
    public function addChild($productSku, $childSku)
    {
        $product = $this->productRepository->get($productSku);
        $child = $this->productRepository->get($childSku);

        $childrenIds = array_values($this->configurableType->getChildrenIds($product->getId())[0]);
        if (in_array($child->getId(), $childrenIds)) {
            throw new StateException('Product has been already attached');
        }

        $childrenIds[] = $child->getId();
        $product->setAssociatedProductIds($childrenIds);
        $product->save();
        return true;
    }
}