<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Model\Plugin;

class BundleOptions
{
    /**
     * @var \Magento\Bundle\Service\V1\Product\Option\WriteService
     */
    protected $optionWriteService;

    /**
     * @var \Magento\Bundle\Service\V1\Product\Option\ReadService
     */
    protected $optionReadService;

    /**
     * @var \Magento\Catalog\Api\Data\ProductDataBuilder
     */
    protected $productBuilder;

    /**
     * @var \Magento\Bundle\Service\V1\Data\Product\OptionBuilder
     */
    protected $optionBuilder;

    /**
     * @var \Magento\Bundle\Service\V1\Data\Product\LinkBuilder
     */
    protected $linkBuilder;

    /**
     * @param \Magento\Bundle\Service\V1\Product\Option\WriteService $optionWriteService
     * @param \Magento\Bundle\Service\V1\Product\Option\ReadService $optionReadService
     * @param \Magento\Catalog\Api\Data\ProductDataBuilder $productBuilder
     * @param \Magento\Bundle\Service\V1\Data\Product\OptionBuilder $optionBuilder
     * @param \Magento\Bundle\Service\V1\Data\Product\LinkBuilder $linkBuilder
     */
    public function __construct(
        \Magento\Bundle\Service\V1\Product\Option\WriteService $optionWriteService,
        \Magento\Bundle\Service\V1\Product\Option\ReadService $optionReadService,
        \Magento\Catalog\Api\Data\ProductDataBuilder $productBuilder,
        \Magento\Bundle\Service\V1\Data\Product\OptionBuilder $optionBuilder,
        \Magento\Bundle\Service\V1\Data\Product\LinkBuilder $linkBuilder
    ) {
        $this->optionWriteService = $optionWriteService;
        $this->optionReadService = $optionReadService;
        $this->productBuilder = $productBuilder;
        $this->optionBuilder = $optionBuilder;
        $this->linkBuilder = $linkBuilder;
    }

    /**
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $subject
     * @param callable $proceed
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param bool $saveOptions
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSave(
        \Magento\Catalog\Api\ProductRepositoryInterface $subject,
        \Closure $proceed,
        \Magento\Catalog\Api\Data\ProductInterface $product,
        $saveOptions = false
    ) {
        /** @var \Magento\Catalog\Api\Data\ProductInterface $result */
        $result = $proceed($product, $saveOptions);

        if ($result->getTypeId() != \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
            return $result;
        }

        /* @var \Magento\Framework\Api\AttributeValue $bundleProductOptionsAttrValue */
        $bundleProductOptionsAttrValue = $product->getData('bundle_product_options');
        if (is_null($bundleProductOptionsAttrValue) || !is_array($bundleProductOptionsAttrValue[0]['product_links'])) {
            $bundleProductOptions = array();
        } else {
            $bundleProductOptions = $bundleProductOptionsAttrValue[0]['product_links'];
        }

        if (is_array($bundleProductOptions)) {
            foreach ($bundleProductOptions as $optionData) {
                $productLink = $this->linkBuilder->setSku($optionData['sku'])->create();
                $option = $this->optionBuilder->setProductLinks([$productLink])->create();
                $this->optionWriteService->addOptionToProduct($result, $option);
            }
        }
        return $result;
    }

    /**
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $subject
     * @param callable $proceed
     * @param string $sku
     * @param bool $editMode
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGet(
        \Magento\Catalog\Api\ProductRepositoryInterface $subject,
        \Closure $proceed,
        $sku,
        $editMode = false
    ) {
        /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
        $product = $proceed($sku, $editMode);
        if ($product->getTypeId() != \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
            return $product;
        }

        $this->productBuilder->populate($product);
        $this->productBuilder->setCustomAttribute(
            'bundle_product_options',
            $this->optionReadService->getListForProduct($product)
        );
        return $this->productBuilder->create();
    }
}
