<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Service\V1\Product\Link;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Webapi\Exception;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

class WriteService implements WriteServiceInterface
{
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    private $productRepository;

    /**
     * @param ProductRepository $productRepository
     */
    public function __construct(
        ProductRepository $productRepository
    ) {
        $this->productRepository = $productRepository;
    }

    /**
     * @inheritdoc
     */
    public function removeChild($productSku, $childSku)
    {
        $product = $this->productRepository->get($productSku);

        if ($product->getTypeId() != Configurable::TYPE_CODE) {
            throw new Exception('Configurable product SKU is expected', Exception::HTTP_FORBIDDEN);
        }

        $options = $product->getTypeInstance()->getUsedProducts($product);
        $ids = array();
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
