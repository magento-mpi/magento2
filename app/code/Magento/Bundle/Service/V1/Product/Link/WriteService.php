<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Service\V1\Product\Link;

use Magento\Catalog\Service\V1\Product\ProductLoader;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\CouldNotSaveException;

class WriteService implements WriteServiceInterface
{
    /**
     * @var ProductLoader
     */
    protected $productLoader;

    /**
     * @var \Magento\Bundle\Model\SelectionFactory $bundleModelSelection
     */
    protected $bundleModelSelection;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Bundle\Model\Resource\BundleFactory
     */
    protected $bundleFactory;

    /**
     * @var \Magento\Bundle\Model\Resource\Option\CollectionFactory
     */
    protected $optionCollection;

    /**
     * @param ProductLoader $productLoader
     * @param \Magento\Bundle\Model\SelectionFactory $bundleModelSelection
     * @param \Magento\Bundle\Model\Resource\BundleFactory $bundleFactory
     * @param \Magento\Bundle\Model\Resource\Option\CollectionFactory $optionCollection,
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        ProductLoader $productLoader,
        \Magento\Bundle\Model\SelectionFactory $bundleModelSelection,
        \Magento\Bundle\Model\Resource\BundleFactory $bundleFactory,
        \Magento\Bundle\Model\Resource\Option\CollectionFactory $optionCollection,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->productLoader = $productLoader;
        $this->bundleModelSelection = $bundleModelSelection;
        $this->bundleFactory = $bundleFactory;
        $this->optionCollection = $optionCollection;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function addChild($productSku, $optionId, Data\ProductLink $linkedProduct)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->productLoader->load($productSku);
        if ($product->getTypeId() != \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
            throw new InputException('Product with specified sku: "%1" is not a bundle product', [$productSku]);
        }

        $options = $this->optionCollection->create();
        $options->setProductIdFilter($product->getId())->joinValues($this->storeManager->getStore()->getId());
        $isNewOption = true;
        foreach ($options as $option) {
            if ($option->getOptionId() == $optionId) {
                $isNewOption = false;
                break;
            }
        }

        if ($isNewOption) {
            throw new InputException(
                'Product with specified sku: "%1" does not contain option: "%2"',
                [$productSku, $optionId]
            );
        }

        /* @var $resource \Magento\Bundle\Model\Resource\Bundle */
        $resource = $this->bundleFactory->create();
        $selections = $resource->getSelectionsData($product->getId());
        /** @var \Magento\Catalog\Model\Product $linkProductModel */
        $linkProductModel = $this->productLoader->load($linkedProduct->getSku());
        if ($selections) {
            foreach ($selections as $selection) {
                if ($selection['option_id'] == $optionId && $selection['product_id'] == $linkProductModel->getId()) {
                    throw new CouldNotSaveException(
                        'Child with specified sku: "%1" already assigned to product: "%2"',
                        [$linkedProduct->getSku(), $productSku]
                    );
                }
            }
        }

        $selectionModel = $this->bundleModelSelection->create();
        $selectionModel->setOptionId($optionId)
            ->setPosition($linkedProduct->getPosition())
            ->setSelectionQty($linkedProduct->getQuantity())
            ->setSelectionPriceType($linkedProduct->getPriceType())
            ->setSelectionPriceValue($linkedProduct->getPriceValue())
            ->setSelectionCanChangeQty($linkedProduct->getCanChangeQuantity())
            ->setProductId($linkProductModel->getId())
            ->setParentProductId($product->getId())
            ->setIsDefault($linkedProduct->getIsDefault())
            ->setWebsiteId($this->storeManager->getStore()->getWebsiteId());

        try {
            $selectionModel->save();
        } catch (\Exception $e) {
            throw new CouldNotSaveException('Could not save child: "%1"', [$e->getMessage()], $e);
        }

        return $selectionModel->getId();
    }
}
