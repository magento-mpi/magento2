<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Pricing\Price;

use Magento\Catalog\Helper\Data;
use Magento\Pricing\Adjustment\CalculatorInterface;
use Magento\Catalog\Model\Product;

/**
 * MSRP price model
 */
class MsrpPrice extends FinalPrice implements MsrpPriceInterface
{
    /**
     * Price type MSRP
     */
    const PRICE_CODE = 'msrp_price';

    /**
     * @var \Magento\Catalog\Helper\Data
     */
    protected $catalogDataHelper;

    /**
     * @param Product $product
     * @param float $quantity
     * @param CalculatorInterface $calculator
     * @param Data $catalogDataHelper
     */
    public function __construct(
        Product $product,
        $quantity,
        CalculatorInterface $calculator,
        Data $catalogDataHelper
    ) {
        parent::__construct($product, $quantity, $calculator);
        $this->catalogDataHelper = $catalogDataHelper;
    }

    /**
     * Returns whether the MSRP should be shown on gesture
     *
     * @return bool
     */
    public function isShowPriceOnGesture()
    {
        return $this->catalogDataHelper->isShowPriceOnGesture($this->salableItem);
    }

    /**
     * Get MAP message for price
     *
     * @return string
     */
    public function getMsrpPriceMessage()
    {
        return $this->catalogDataHelper->getMsrpPriceMessage($this->salableItem);
    }

    /**
     * Returns true in case MSRP is enabled
     *
     * @return bool
     */
    public function isMsrpEnabled()
    {
        return $this->catalogDataHelper->isMsrpEnabled();
    }

    /**
     * Check if can apply Minimum Advertise price to product
     *
     * @param Product $product
     * @return bool
     */
    public function canApplyMsrp(Product $product)
    {
        return $this->catalogDataHelper->canApplyMsrp($product);
    }
}
