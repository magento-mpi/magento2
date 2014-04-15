<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pricing
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Pricing\Render;

use Magento\View\Element\Template;
use Magento\Pricing\Render\AbstractAdjustment;
use Magento\Pricing\PriceCurrencyInterface;

/**
 * @method string getIdSuffix()
 * @method string getDisplayLabel()
 */
class Adjustment extends AbstractAdjustment
{
    /**
     * @var \Magento\Tax\Helper\Data
     */
    protected $taxHelper;

    /**
     * @param Template\Context $context
     * @param \Magento\Tax\Helper\Data $helper
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Tax\Helper\Data $helper,
        array $data = []
    ) {
        $this->taxHelper = $helper;
        parent::__construct($context, $priceCurrency, $data);
    }

    /**
     * @return null
     */
    protected function apply()
    {
        $html = $this->toHtml();
        if ($this->displayBothPrices()) {
            if ($this->getZone() !== \Magento\Pricing\Render::ZONE_ITEM_OPTION) {
                $this->amountRender->setPriceDisplayLabel(__('Excl. Tax:'));
            }
            $this->amountRender->setPriceId(
                $this->buildIdWithPrefix('price-excluding-tax-')
            );
            $this->amountRender->setDisplayValue(
                $this->amountRender->getDisplayValue() -
                $this->amountRender->getAmount()->getAdjustmentAmount($this->getAdjustmentCode())
            );
        } elseif ($this->displayPriceExcludingTax()) {

            $this->amountRender->setDisplayValue(
                $this->amountRender->getDisplayValue() -
                $this->amountRender->getAmount()->getAdjustmentAmount($this->getAdjustmentCode())
            );
        }
        if (trim($html)) {
            $this->amountRender->addAdjustmentHtml($this->getAdjustmentCode(), $html);
        }
    }

    /**
     * Obtain code of adjustment type
     *
     * @return string
     */
    public function getAdjustmentCode()
    {
        //@TODO We can build two model using DI, not code. What about passing it in constructor?
        return \Magento\Tax\Pricing\Adjustment::CODE;
    }

    /**
     * Define if both prices should be displayed
     *
     * @return bool
     */
    public function displayBothPrices()
    {
        return $this->taxHelper->displayBothPrices();
    }

    /**
     * Obtain display amount excluding tax
     *
     * @return string
     */
    public function getDisplayAmountExclTax()
    {
        // todo use 'excludeWith' method instead hard-coded list here
        return $this->convertAndFormatCurrency($this->amountRender->getAmount()->getValue(['tax', 'weee']), false);
    }

    /**
     * Obtain display amount
     *
     * @param bool $includeContainer
     * @return string
     */
    public function getDisplayAmount($includeContainer = true)
    {
         return $this->convertAndFormatCurrency($this->amountRender->getAmount()->getValue(), $includeContainer);
    }

    /**
     * Build identifier with prefix
     *
     * @param string $prefix
     * @return string
     */
    public function buildIdWithPrefix($prefix)
    {
        $priceId = $this->getPriceId();
        if (!$priceId) {
            $priceId = $this->getSaleableItem()->getId();
        }
        return $prefix . $priceId . $this->getIdSuffix();
    }

    /**
     * Should be displayed price including tax
     *
     * @return bool
     */
    public function displayPriceIncludingTax()
    {
        return $this->taxHelper->displayPriceIncludingTax();
    }

    /**
     * Should be displayed price excluding tax
     *
     * @return bool
     */
    public function displayPriceExcludingTax()
    {
        return $this->taxHelper->displayPriceExcludingTax();
    }
}
