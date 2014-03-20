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


class FinalPriceBox extends PriceBox
{
    /**
     * @param string $priceType
     * @param SaleableInterface $object
     * @param array $arguments
     * @return string
     */
    public function render($priceType, SaleableInterface $object, array $arguments = [])
    {
        /** @var MsrpPrice $msrpPriceType */
        $msrpPriceType = $this->getPriceType('msrp');
        if (!$msrpPriceType->isMsrpEnabled()) {
            return parent::render($priceType, $object, $arguments);
        }

        /** @var PriceBox $msrpBlock */
        $msrpBlock = $this->getChildBlock('default.msrp');
        if ($msrpBlock instanceof Render) {
            return $msrpBlock->render('msrp', $object, $arguments);
        }

        return parent::render($priceType, $object, $arguments);
    }
}
