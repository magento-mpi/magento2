<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Pricing\Render;

use Magento\Framework\Pricing\Render\PriceBox as BasePriceBox;
use Magento\Catalog\Pricing\Price\MsrpPrice;
use Magento\Framework\Pricing\Render;
use Magento\Catalog\Pricing\Price;
use Magento\Catalog\Pricing\Price\MsrpPriceInterface;

/**
 * Class for final_price rendering
 *
 * @method bool getUseLinkForAsLowAs()
 * @method bool getDisplayMinimalPrice()
 */
class FinalPriceBox extends BasePriceBox
{
    /**
     * @return string
     */
    protected function _toHtml()
    {
        $result = parent::_toHtml();

        try {
            /** @var MsrpPrice $msrpPriceType */
            $msrpPriceType = $this->getSaleableItem()->getPriceInfo()->getPrice('msrp_price');
        } catch (\InvalidArgumentException $e) {
            $this->_logger->logException($e);
            return $this->wrapResult($result);
        }

        //Renders MAP price in case it is enabled
        if ($msrpPriceType->canApplyMsrp($this->getSaleableItem())) {
            /** @var BasePriceBox $msrpBlock */
            $msrpBlock = $this->rendererPool->createPriceRender(
                MsrpPriceInterface::PRICE_TYPE_MSRP,
                $this->getSaleableItem(),
                [
                    'real_price_html' => $result
                ]
            );
            $result = $msrpBlock->toHtml();
        }

        return $this->wrapResult($result);
    }

    /**
     * Wrap with standard required container
     *
     * @param string $html
     * @return string
     */
    protected function wrapResult($html)
    {
        return '<div class="price-box ' . $this->getData('css_classes') . '">' . $html . '</div>';
    }

    /**
     * Render minimal amount
     *
     * @return string
     */
    public function renderAmountMinimal()
    {
        //@TODO Implement 'minimal_price' final price is a minimum price

        $price = $this->getPriceType(\Magento\Catalog\Pricing\Price\FinalPriceInterface::PRICE_TYPE_FINAL);
        $id = $this->getPriceId() ? $this->getPriceId() : 'product-minimal-price-' . $this->getSaleableItem()->getId();
        return $this->renderAmount(
            $price->getMinimalPrice(),
            [
                'display_label'     => __('As low as:'),
                'price_id'          => $id,
                'include_container' => false,
                'skip_adjustments' => true
            ]
        );
    }

    /**
     * Define if the special price should be shown
     *
     * @return bool
     */
    public function hasSpecialPrice()
    {
        $displayRegularPrice = $this->getPriceType(Price\RegularPrice::PRICE_TYPE_PRICE_DEFAULT)->getAmount();
        $displayFinalPrice = $this->getPriceType(Price\FinalPriceInterface::PRICE_TYPE_FINAL)->getAmount();
        return $displayFinalPrice < $displayRegularPrice;
    }

    /**
     * Define if the minimal price should be shown
     *
     * @return bool
     */
    public function showMinimalPrice()
    {
        /** @var Price\FinalPrice $finalPrice */
        $finalPrice = $this->getPriceType(Price\FinalPriceInterface::PRICE_TYPE_FINAL);
        $finalPriceValue = $finalPrice->getAmount()->getValue();
        $minimalPriceAValue = $finalPrice->getMinimalPrice()->getValue();
        return $this->getDisplayMinimalPrice()
        && $minimalPriceAValue
        && $minimalPriceAValue < $finalPriceValue;
    }
}
