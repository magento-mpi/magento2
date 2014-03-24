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
use Magento\Pricing\Render\PriceBox;
use Magento\Catalog\Pricing\Price\MsrpPrice;
use Magento\Pricing\Render;

/**
 * Class for final_price rendering
 *
 * @method bool getUseLinkForAsLowAs()
 * @method float getDisplayMinimalPrice()
 */
class FinalPriceBox extends PriceBox
{
    /**
     * Renders MAP price in case it is enabled
     *
     * @param string $priceType
     * @param SaleableInterface $saleableItem
     * @param array $arguments
     * @return string
     */
    public function render($priceType, SaleableInterface $saleableItem, array $arguments = [])
    {
        $result = parent::render($priceType, $saleableItem, $arguments);

        try {
            /** @var MsrpPrice $msrpPriceType */
            $msrpPriceType = $saleableItem->getPriceInfo()->getPrice('msrp');
        } catch (\InvalidArgumentException $e) {
            $this->_logger->logException($e);
            return $this->wrapResult($result);
        }
        if ($msrpPriceType->canApplyMsrp($saleableItem)) {
            /** @var PriceBox $msrpBlock */
            $msrpBlock = $this->getChildBlock('default.msrp');
            if ($msrpBlock instanceof PriceBox) {
                $arguments['real_price_html'] = $this->wrapResult($result);
                $result = $msrpBlock->render('msrp', $saleableItem, $arguments);
            }
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
    
    public function renderAmountMinimal()
    {
        //@TODO Implement 'minimal_price'

        $price = $this->getPriceType('minimal_price');
        $id = $this->getPriceId() ? $this->getPriceId() : 'product-minimal-price-' . $this->getSaleableItem()->getId();
        return $this->renderAmount($price, [
            'display_label'     => __('As low as:'),
            'price_id'          => $id,
            'include_container' => false
        ]);
    }

    public function showSpecialPrice()
    {
        $displayRegularPrice = $this->getPriceType('price')->getDisplayValue();
        $displayFinalPrice = $this->getPriceType('final_price')->getDisplayValue();

        return $displayFinalPrice < $displayRegularPrice;
    }

    public function showMinimalPrice()
    {
        $displayFinalPrice = $this->getPriceType('final_price')->getDisplayValue();
        $minimalPrice = $this->getPriceType('final_price')->getMinimalPrice();

        return $this->getDisplayMinimalPrice() && $minimalPrice && $minimalPrice < $displayFinalPrice;
    }
}
