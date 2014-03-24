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
            if ($msrpPriceType->canApplyMsrp($saleableItem)) {
                /** @var PriceBox $msrpBlock */
                $msrpBlock = $this->getChildBlock('default.msrp');
                if ($msrpBlock instanceof PriceBox) {
                    $arguments['real_price_html'] = $this->wrapResult($result);
                    $result = $msrpBlock->render('msrp', $saleableItem, $arguments);
                }
            }
        } catch (\InvalidArgumentException $e) {
            $this->_logger->logException($e);
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
}
