<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Msrp\Model;

use Magento\Catalog\Model\Resource\Eav\AttributeFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Msrp\Model\Product\Attribute\Source\Type\Price as TypePrice;
use Magento\Msrp\Model\Product\Options;
use Magento\Store\Model\StoreManagerInterface;

class Msrp
{
    /**
     * @var array
     */
    protected $mapApplyToProductType = null;

    /**
     * @var AttributeFactory
     */
    protected $eavAttributeFactory;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Options
     */
    protected $options;

    /**
     * @param AttributeFactory $eavAttributeFactory
     * @param ProductFactory $productFactory
     * @param Config $config
     */
    public function __construct(
        AttributeFactory $eavAttributeFactory,
        ProductFactory $productFactory,
        StoreManagerInterface $storeManager,
        \Magento\Msrp\Model\Config $config,
        Options $options
    ) {
        $this->eavAttributeFactory = $eavAttributeFactory;
        $this->config = $config;
        $this->productFactory = $productFactory;
        $this->storeManager = $storeManager;
        $this->options = $options;
    }

    /**
     * Check whether MAP applied to product Product Type
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    public function canApplyToProduct($product)
    {
        if ($this->mapApplyToProductType === null) {
            /** @var $attribute \Magento\Catalog\Model\Resource\Eav\Attribute */
            $attribute = $this->eavAttributeFactory->create()->loadByCode(Product::ENTITY, 'msrp');
            $this->mapApplyToProductType = $attribute->getApplyTo();
        }
        return in_array($product->getTypeId(), $this->mapApplyToProductType);
    }

    /**
     * Check if can apply Minimum Advertise price to product
     * in specific visibility
     *
     * @param int|\Magento\Catalog\Model\Product $product
     * @param int|null $visibility Check displaying price in concrete place (by default generally)
     * @return bool
     */
    public function canApplyMsrp($product, $visibility = null)
    {
        if (!$this->config->isEnabled()) {
            return false;
        }
        if (is_numeric($product)) {
            $product = $this->productFactory->create()
                ->setStoreId($this->storeManager->getStore()->getId())
                ->load($product);
        }
        $result = $this->canApplyToProduct($product);
        if ($result && $visibility !== null) {
            $productPriceVisibility = $product->getMsrpDisplayActualPriceType();
            if ($productPriceVisibility == TypePrice::TYPE_USE_CONFIG) {
                $productPriceVisibility = $this->config->getDisplayActualPriceType();
            }
            $result = $productPriceVisibility == $visibility;
        }

        if ($product->getTypeInstance()->isComposite($product) && (!$result || $visibility !== null)) {
            $isEnabledInOptions = $this->options->isEnabled($product, $visibility);
            if ($isEnabledInOptions !== null) {
                $result = $isEnabledInOptions;
            }
        }

        return $result;
    }
}
