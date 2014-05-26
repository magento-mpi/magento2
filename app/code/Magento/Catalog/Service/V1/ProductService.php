<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

use Magento\Catalog\Controller\Adminhtml\Product;
use Magento\Framework\Exception\NoSuchEntityException;

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
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;


    /**
     * @param Product\Initialization\Helper $initializationHelper
     * @param Product\Builder $productBuilder
     * @param \Magento\Catalog\Model\Product\TypeTransitionManager $productTypeManager
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     */
    public function __construct(
        Product\Initialization\Helper $initializationHelper,
        Product\Builder $productBuilder,
        \Magento\Catalog\Model\Product\TypeTransitionManager $productTypeManager,
        \Magento\Catalog\Model\ProductFactory $productFactory
    )
    {
        $this->initializationHelper = $initializationHelper;
        $this->productBuilder = $productBuilder;
        $this->productTypeManager = $productTypeManager;
        $this->productFactory = $productFactory;
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
    {}

    /**
     * {@inheritdoc}
     */
    public function getAll(\Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria)
    {}
}
