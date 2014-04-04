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
use Magento\Pricing\Object\SaleableInterface;

/**
 * MSRP price model
 */
class MsrpPrice extends FinalPrice implements MsrpPriceInterface
{
    /**
     * @var string
     */
    protected $priceType = self::PRICE_TYPE_MSRP;

    /**
     * @var \Magento\Catalog\Helper\Data
     */
    protected $catalogDataHelper;

    /**
     * @param SaleableInterface $salableItem
     * @param float $quantity
     * @param CalculatorInterface $calculator
     * @param Data $catalogDataHelper
     */
    public function __construct(
        SaleableInterface $salableItem,
        $quantity,
        CalculatorInterface $calculator,
        Data $catalogDataHelper
    ) {
        parent::__construct($salableItem, $quantity, $calculator);
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
     * @param SaleableInterface $saleableItem
     * @return bool
     */
    public function canApplyMsrp(SaleableInterface $saleableItem)
    {
        return $this->catalogDataHelper->canApplyMsrp($saleableItem);
    }
}
