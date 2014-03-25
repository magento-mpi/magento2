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

use Magento\View\Element\Template;
use Magento\Pricing\Render\AbstractAdjustment;
use Magento\Pricing\PriceCurrencyInterface;
use Magento\Weee\Model\Tax;

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
        //@TODO We can build two model using DI, not code. What about passing it in constructor?
        return \Magento\Weee\Pricing\Adjustment::CODE;
    }

    public function showInclDescr()
    {
        return $this->getWeeeTaxAmount() && $this->typeOfDisplay(Tax::DISPLAY_INCL_DESCR);
    }

    public function showExclDescrIncl()
    {
        return $this->getWeeeTaxAmount() && $this->typeOfDisplay(Tax::DISPLAY_EXCL_DESCR_INCL);
    }

    /**
     * @return array|\Magento\Object[]
     */
    public function getWeeeTaxAttributes()
    {
        return $this->isDisplayFpt() ? $this->getWeeeAttributesForDisplay() : [];
    }

    /**
     * @param \Magento\Object $attribute
     * @return string
     */
    public function renderWeeeTaxAttribute(\Magento\Object $attribute)
    {
        return $attribute->getData('name') . ': ' . $this->convertAndFormatCurrency($attribute->getData('amount'));
    }

    /**
     * @param int|int[]|null $compareTo
     * @param string|null $zone
     * @param \Magento\Core\Model\Store|null $store
     * @return bool|int
     */
    protected function typeOfDisplay($compareTo = null, $zone = null, $store = null)
    {
        return $this->weeeHelper->typeOfDisplay($compareTo, $zone, $store);
    }

    /**
     * @return float
     */
    protected function getAmount()
    {
        $product = $this->getSaleableItem();
        return $this->weeeHelper->getAmount($product);
    }

    /**
     * @return \Magento\Object[]
     */
    protected function getWeeeAttributesForDisplay()
    {
        $product = $this->getSaleableItem();
        return $this->weeeHelper->getProductWeeeAttributesForDisplay($product);
    }

    /**
     * @return float|null
     */
    protected function getWeeeTaxAmount()
    {
        return $this->isDisplayFpt() ? $this->getAmount() : null;
    }

    /**
     * @return bool
     */
    protected function isDisplayFpt()
    {
        $isDisplayFpt = $this->typeOfDisplay(
            [
                Tax::DISPLAY_INCL_DESCR,
                Tax::DISPLAY_EXCL_DESCR_INCL
            ]
        );

        return $isDisplayFpt;
    }
}
