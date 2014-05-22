<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Catalog\Service\V1\Product\Link;
use \Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks as LinksInitializer;

class WriteService implements WriteServiceInterface
{
    /**
     * @var LinksInitializer
     */
    protected $linkInitializer;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @param LinksInitializer $linkInitializer
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     */
    public function __construct(
        LinksInitializer $linkInitializer,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        $this->linkInitializer = $linkInitializer;
        $this->productFactory = $productFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function assign($productId, array $assignedProducts, $linkType)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->productFactory->create();
        $product->load($productId);

        $links = [];
        /** @var Data\LinkedProductEntity[] $assignedProducts*/
        foreach ($assignedProducts as $linkedProduct) {
            $links[] = (array) $linkedProduct;
        }
        $this->linkInitializer->initializeLinks($product, [$linkType => $links]);
        $product->save();
    }
}
