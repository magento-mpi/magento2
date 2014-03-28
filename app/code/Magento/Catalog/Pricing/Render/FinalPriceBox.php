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

use Magento\Pricing\Object\SaleableInterface;
use Magento\Pricing\Render\PriceBox as BasePriceBox;
use Magento\Catalog\Pricing\Price\MsrpPrice;
use Magento\Pricing\Render;

/**
 * Class for final_price rendering
 *
 * @method bool getUseLinkForAsLowAs()
 * @method float getDisplayMinimalPrice()
 */
class FinalPriceBox extends BasePriceBox
{
    protected function _toHtml()
    {
        $result = parent::_toHtml();

        try {
            /** @var MsrpPrice $msrpPriceType */
            $msrpPriceType = $this->getSaleableItem()->getPriceInfo()->getPrice('msrp');
        } catch (\InvalidArgumentException $e) {
            $this->_logger->logException($e);
            return $this->wrapResult($result);
        }

        //Renders MAP price in case it is enabled
        if ($msrpPriceType->canApplyMsrp($this->getSaleableItem())) {
            /** @var BasePriceBox $msrpBlock */
            $msrpBlock = $this->rendererPool->createPriceRender(
                'msrp',
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

        $price = $this->getPriceType('final_price');
        $id = $this->getPriceId() ? $this->getPriceId() : 'product-minimal-price-' . $this->getSaleableItem()->getId();
        return $this->renderAmount(
            $price->getMinimalPrice(),
            [
                'display_label'     => __('As low as:'),
                'price_id'          => $id,
                'include_container' => false
            ]
        );
    }

    /**
     * Define if the special price should be shown
     *
     * @return bool
     */
    public function showSpecialPrice()
    {
        $displayRegularPrice = $this->getPriceType('regular_price')->getAmount();
        $displayFinalPrice = $this->getPriceType('final_price')->getAmount();
        return $displayFinalPrice < $displayRegularPrice;
    }

    /**
     * Define if the minimal price should be shown
     *
     * @return bool
     */
    public function showMinimalPrice()
    {
        $displayFinalPrice = $this->getPriceType('final_price')->getAmount();
        $minimalPrice = $this->getPriceType('final_price')->getMinimalPrice();

        return $this->getDisplayMinimalPrice() && $minimalPrice && $minimalPrice < $displayFinalPrice;
    }
}
