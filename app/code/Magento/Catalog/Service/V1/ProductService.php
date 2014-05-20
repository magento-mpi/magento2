<?php

namespace Magento\Catalog\Service\V1;

use Magento\Framework\Service\V1\Data\SearchCriteria;
use Magento\Catalog\Controller\Adminhtml\Product;

class ProductService
{
    /**
     * @var Product\Initialization\Helper
     */
    protected $initializationHelper;

    /**
     * @var Product\Builder
     */
    protected $productBuilder;

    /**
     * @var \Magento\Catalog\Model\Product\TypeTransitionManager
     */
    protected $productTypeManager;

    public function __construct(
        Product\Initialization\Helper $initializationHelper,
        Product\Builder $productBuilder,
        \Magento\Catalog\Model\Product\TypeTransitionManager $productTypeManager
    )
    {
        $this->initializationHelper = $initializationHelper;
        $this->productBuilder = $productBuilder;
        $this->productTypeManager = $productTypeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(Data\Product $productDto)
    {
        $product = $this->productBuilder->build($productDto);
        $this->productTypeManager->processProduct($product);

        if (!$product->getId()) {
            throw new \Magento\Framework\Model\Exception(__('Unable to save product'));
        }
        $product->save();
        return $product->getId();
    }
}
