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
     * Save product process
     *
     * @param \Magento\Catalog\Service\V1\Data\Product $product
     * @throws \Magento\Framework\Exception\InputException If there is a problem with the input
     * @throws \Magento\Framework\Exception\NoSuchEntityException If a ID is sent but the entity does not exist
     * @throws \Magento\Framework\Model\Exception If something goes wrong during save
     * @return int ID
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
}
