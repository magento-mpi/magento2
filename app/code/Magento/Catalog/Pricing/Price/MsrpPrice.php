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
class MsrpPrice extends FinalPrice
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
     * @param Product $product
     * @param Data $catalogDataHelper
     */
    public function __construct(Product $product, Data $catalogDataHelper)
    {
        $this->catalogDataHelper = $catalogDataHelper;
        parent::__construct($product);
    }

    /**
     * @return bool
     */
    public function isShowPriceOnGesture()
    {
        return $this->catalogDataHelper->isShowPriceOnGesture($this->product);
    }

    /**
     * Get MAP message for price
     *
     * @return string
     */
    public function getMsrpPriceMessage()
    {
        return $this->catalogDataHelper->getMsrpPriceMessage($this->product);
    }
}
