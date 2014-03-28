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
     * Weee helper
     *
     * @var \Magento\Weee\Helper\Data
     */
    protected $weeeHelper;

    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Weee\Helper\Data $weeeHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Weee\Helper\Data $weeeHelper,
        array $data = []
    ) {
        $this->weeeHelper = $weeeHelper;
        parent::__construct($context, $priceCurrency, $data);
    }

    /**
     * Obtain adjustment code
     *
     * @return string
     */
    public function getAdjustmentCode()
    {
        //@TODO We can build two model using DI, not code. What about passing it in constructor?
        return \Magento\Weee\Pricing\Adjustment::CODE;
    }

    /**
     * Get weee amount
     *
     * @return float
     */
    protected function getWeeeTaxAmount()
    {
        $product = $this->getSaleableItem();
        return $this->weeeHelper->getAmount($product);
    }

    /**
     * Define if adjustment should be shown with including tax, description
     *
     * @return bool
     */
    public function showInclDescr()
    {
        return $this->isDisplayFpt() && $this->getWeeeTaxAmount() && $this->typeOfDisplay(Tax::DISPLAY_INCL_DESCR);
    }

    /**
     * Define if adjustment should be shown with including tax, excluding tax, description
     *
     * @return bool
     */
    public function showExclDescrIncl()
    {
        return $this->isDisplayFpt() && $this->getWeeeTaxAmount() && $this->typeOfDisplay(Tax::DISPLAY_EXCL_DESCR_INCL);
    }

    /**
     * Obtain Weee tax attributes
     *
     * @return array|\Magento\Object[]
     */
    public function getWeeeTaxAttributes()
    {
        return $this->isDisplayFpt() ? $this->getWeeeAttributesForDisplay() : [];
    }

    /**
     * Render Weee tax attributes
     *
     * @param \Magento\Object $attribute
     * @return string
     */
    public function renderWeeeTaxAttribute(\Magento\Object $attribute)
    {
        return $attribute->getData('name') . ': ' . $this->convertAndFormatCurrency($attribute->getData('amount'));
    }

    /**
     * Returns display type for price accordingly to current zone
     *
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
     * Get Weee attributes for display
     *
     * @return \Magento\Object[]
     */
    protected function getWeeeAttributesForDisplay()
    {
        $product = $this->getSaleableItem();
        return $this->weeeHelper->getProductWeeeAttributesForDisplay($product);
    }

    /**
     * Define if the FPT should be displayed
     *
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
