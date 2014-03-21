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

use Magento\Catalog\Model\Product;
use Magento\Catalog\Helper\Data;

/**
 * MSRP price model
 */
class MsrpPrice extends FinalPrice implements MsrpPriceInterface
{
    /**
     * @var string
     */
    protected $priceType = 'msrp';

    /**
     * @var \Magento\Catalog\Helper\Data
     */
    protected $catalogDataHelper;

    /**
     * @param \Magento\Catalog\Model\Product $salableItem
     * @param Data $catalogDataHelper
     * @param float $quantity
     */
    public function __construct(Product $salableItem, Data $catalogDataHelper, $quantity)
    {
        $this->catalogDataHelper = $catalogDataHelper;
        parent::__construct($salableItem, $quantity);
    }

    /**
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
}
