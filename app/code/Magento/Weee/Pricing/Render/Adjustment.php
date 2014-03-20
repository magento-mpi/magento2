<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pricing
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Weee\Pricing\Render;

use Magento\Pricing\Object\SaleableInterface;
use Magento\View\Element\Template;
use Magento\Pricing\Render\AbstractAdjustment;
use Magento\Pricing\PriceCurrencyInterface;

class Adjustment extends AbstractAdjustment
{
    /**
     * @var \Magento\Weee\Helper\Data
     */
    protected $weeeHelper;

    /**
     * @param Template\Context $context
     * @param \Magento\Catalog\Helper\Product\Price $helper
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Weee\Helper\Data $weeeHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Catalog\Helper\Product\Price $helper,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Weee\Helper\Data $weeeHelper,
        array $data = []
    ) {
        $this->weeeHelper = $weeeHelper;
        parent::__construct($context, $helper, $priceCurrency, $data);
    }

    /**
     * @return string
     */
    public function getAdjustmentCode()
    {
        return 'weee';
    }

    /**
     * @param int|int[]|null $compareTo
     * @param string|null $zone
     * @param \Magento\Core\Model\Store|null $store
     * @return bool|int
     */
    public function typeOfDisplay($compareTo = null, $zone = null, $store = null)
    {
        return $this->weeeHelper->typeOfDisplay($compareTo, $zone, $store);
    }

    /**
     * @param SaleableInterface $product
     * @return float
     */
    public function getAmount(SaleableInterface $product)
    {
        return $this->weeeHelper->getAmount($product);
    }

    /**
     * @param SaleableInterface $product
     * @return \Magento\Object[]
     */
    public function getProductWeeeAttributesForDisplay(SaleableInterface $product)
    {
        return $this->weeeHelper->getProductWeeeAttributesForDisplay($product);
    }
}
