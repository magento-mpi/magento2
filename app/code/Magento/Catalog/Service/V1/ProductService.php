<?php

namespace Magento\Catalog\Service\V1;

use Magento\Catalog\Controller\Adminhtml\Product;

class ProductService implements ProductServiceInterface
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

    /**
     * @param Product\Initialization\Helper $initializationHelper
     * @param Product\Builder $productBuilder
     * @param \Magento\Catalog\Model\Product\TypeTransitionManager $productTypeManager
     */
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
    public function save(\Magento\Catalog\Service\V1\Data\Product $product)
    {
        $product = $this->productBuilder->build($product);
        $this->initializationHelper->initialize($product);
        $this->productTypeManager->processProduct($product);
        $product->save();
        if (!$product->getId()) {
            throw new \Magento\Framework\Model\Exception(__('Unable to save product'));
        }
        return $product->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {}

    /**
     * {@inheritdoc}
     */
    public function getAll(\Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria)
    {}
}
