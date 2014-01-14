<?php
/**
 * Catalog product copier. Creates product duplicate
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Product;

class Copier 
{
    /**
     * @var CopyConstructorInterface
     */
    protected $copyConstructor;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @param CopyConstructorInterface $copyConstructor
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     */
    public function __construct(
        CopyConstructorInterface $copyConstructor,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        $this->productFactory  = $productFactory;
        $this->copyConstructor = $copyConstructor;
    }

    /**
     * Create product duplicate
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Catalog\Model\Product
     */
    public function copy(\Magento\Catalog\Model\Product $product)
    {
        $product->getWebsiteIds();
        $product->getCategoryIds();

        $duplicate = $this->productFactory->create();
        $duplicate->setData($product->getData());
        $duplicate->setIsDuplicate(true);
        $duplicate->setOriginalId($product->getId());
        $duplicate->setStatus(\Magento\Catalog\Model\Product\Status::STATUS_DISABLED);
        $duplicate->setCreatedAt(null);
        $duplicate->setUpdatedAt(null);
        $duplicate->setId(null);
        $duplicate->setStoreId(\Magento\Core\Model\Store::DEFAULT_STORE_ID);

        $this->copyConstructor->build($product, $duplicate);
        $duplicate->save();

        $product->getOptionInstance()->duplicate($product->getId(), $duplicate->getId());
        $product->getResource()->duplicate($product->getId(), $duplicate->getId());
        return $duplicate;
    }
} 
