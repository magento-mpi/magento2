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
     * @var \Magento\Catalog\Service\V1\Data\ProductMapper
     */
    protected $productMapper;

    /**
     * @var \Magento\Catalog\Model\Product\TypeTransitionManager
     */
    protected $productTypeManager;

    /**
     * @param Product\Initialization\Helper $initializationHelper
     * @param Data\ProductMapper $productMapper
     * @param \Magento\Catalog\Model\Product\TypeTransitionManager $productTypeManager
     */
    public function __construct(
        Product\Initialization\Helper $initializationHelper,
        \Magento\Catalog\Service\V1\Data\ProductMapper $productMapper,
        \Magento\Catalog\Model\Product\TypeTransitionManager $productTypeManager
    ) {
        $this->initializationHelper = $initializationHelper;
        $this->productMapper = $productMapper;
        $this->productTypeManager = $productTypeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function create(\Magento\Catalog\Service\V1\Data\Product $product)
    {
        $productModel = $this->productMapper->toModel($product);
        $this->initializationHelper->initialize($productModel);
        $this->productTypeManager->processProduct($productModel);
        $productModel->save();
        if (!$productModel->getId()) {
            throw new \Magento\Framework\Model\Exception(__('Unable to save product'));
        }
        return $productModel->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function update(\Magento\Catalog\Service\V1\Data\Product $product)
    {
        $productModel = $this->productMapper->toModel($product);
        $productModel->setId(null)->load($product->getId());
        if (!$productModel->getId()) {
            throw new \Magento\Framework\Model\Exception(__('Product is not exists'));
        }

        $this->initializationHelper->initialize($productModel);
        $this->productTypeManager->processProduct($productModel);
        $productModel->save();
        return $productModel->getId();
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
