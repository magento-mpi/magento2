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
 * Final price box renderer
 */
class FinalPriceBox extends PriceBox
{
    /**
     * @param string $priceType
     * @param SaleableInterface $saleableItem
     * @param array $arguments
     * @return string
     */
    public function render($priceType, SaleableInterface $saleableItem, array $arguments = [])
    {
        /** @var MsrpPrice $msrpPriceType */
        $msrpPriceType = $saleableItem->getPriceInfo()->getPrice('msrp');
        if (!$msrpPriceType->isMsrpEnabled()) {
            return $this->wrapResult(parent::render($priceType, $saleableItem, $arguments));
        }

        /** @var PriceBox $msrpBlock */
        $msrpBlock = $this->getChildBlock('default.msrp');
        if ($msrpBlock instanceof Render) {
            return $msrpBlock->render('msrp', $saleableItem, $arguments);
        }

        return $this->wrapResult(parent::render($priceType, $saleableItem, $arguments));
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
