<?php

namespace Magento\Catalog\Service\V1;

use Magento\Catalog\Controller\Adminhtml\Product;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\InputException;

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
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @param Product\Initialization\Helper $initializationHelper
     * @param Data\ProductMapper $productMapper
     * @param \Magento\Catalog\Model\Product\TypeTransitionManager $productTypeManager
     */
    public function __construct(
        Product\Initialization\Helper $initializationHelper,
        \Magento\Catalog\Service\V1\Data\ProductMapper $productMapper,
        \Magento\Catalog\Model\Product\TypeTransitionManager $productTypeManager,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        $this->initializationHelper = $initializationHelper;
        $this->productMapper = $productMapper;
        $this->productTypeManager = $productTypeManager;
        $this->productFactory = $productFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create(\Magento\Catalog\Service\V1\Data\Product $product)
    {
        $productModel = $this->productMapper->toModel($product);
        $this->initializationHelper->initialize($productModel);
        $this->productTypeManager->processProduct($productModel);
        $productModel->validate();
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
        $productModel = $this->productFactory->create();
        if (!$productModel->getId()) {
            throw NoSuchEntityException::singleField('id', $product->getId());
        }
        $this->productMapper->toModel($product, $productModel);
        $this->initializationHelper->initialize($productModel);
        $this->productTypeManager->processProduct($productModel);
        $productModel->validate();
        $productModel->save();
        return $productModel->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        $product = $this->productFactory->create();
        $product->load($id);
        if (!$product->getId()) {
            // product does not exist
            throw NoSuchEntityException::singleField('id', $id);
        }
        $product->delete();
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $product = $this->productFactory->create();
        $product->load($id);
        if (!$product->getId()) {
            // product does not exist
            throw NoSuchEntityException::singleField('id', $id);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAll(\Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria)
    {}
}
