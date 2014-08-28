<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Msrp\Model\Product;

use Magento\Msrp\Model\Product\Attribute\Source\Type\Price as TypePrice;

class Options
{
    /**
     * @var \Magento\Msrp\Helper\Data
     */
    protected $msrpData;

    /**
     * @var \Magento\Msrp\Model\Config
     */
    protected $config;

    /**
     * @param \Magento\Msrp\Helper\Data $msrpData
     */
    public function __construct(
        \Magento\Msrp\Helper\Data $msrpData,
        \Magento\Msrp\Model\Config $config
    ) {
        $this->msrpData = $msrpData;
        $this->config = $config;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param null $visibility
     * @return bool|null
     */
    public function isEnabled($product, $visibility = null)
    {
        $result = null;
        /** @var \Magento\Catalog\Model\Product[] $collection */
        $collection = $product->getTypeInstance()->getAssociatedProducts($product)?: [];
        $visibilities = [];
        foreach ($collection as $item) {
            if ($this->msrpData->canApplyMsrp($item)) {
                $visibilities[] = $item->getMsrpDisplayActualPriceType() == TypePrice::TYPE_USE_CONFIG
                    ? $this->config->getDisplayActualPriceType()
                    : $item->getMsrpDisplayActualPriceType();
                $result = true;
            }
        }

        if ($result && $visibility !== null) {
            if ($visibilities) {
                $maxVisibility = max($visibilities);
                $result = $result && $maxVisibility == $visibility;
            } else {
                $result = false;
            }
        }

        return $result;
    }
}
