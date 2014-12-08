<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Service\V1\Product\Link;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\ConfigurableProduct\Model\Resource\Product\Type\Configurable;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Webapi\Exception;

class WriteService implements WriteServiceInterface
{
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var Configurable
     */
    protected $configurableType;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param Configurable $configurableType
     * @internal param ConfigurableFactory $typeConfigurableFactory
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
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

    /**
     * {@inheritdoc}
     */
    public function removeChild($productSku, $childSku)
    {
        $product = $this->productRepository->get($productSku);

        if ($product->getTypeId() != \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            throw new Exception(
                sprintf('Product with specified sku: %s is not a configurable product', $productSku),
                Exception::HTTP_FORBIDDEN
            );
        }

        $options = $product->getTypeInstance()->getUsedProducts($product);
        $ids = [];
        foreach ($options as $option) {
            if ($option->getSku() == $childSku) {
                continue;
            }
            $ids[] = $option->getId();
        }
        if (count($options) == count($ids)) {
            throw new NoSuchEntityException('Requested option doesn\'t exist');
        }
        $product->addData(['associated_product_ids' => $ids]);
        $product->save();

        return true;
    }
}
