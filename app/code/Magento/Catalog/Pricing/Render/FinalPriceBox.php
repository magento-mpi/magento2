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
     * @param SaleableInterface $object
     * @param array $arguments
     * @return string
     */
    public function render($priceType, SaleableInterface $object, array $arguments = [])
    {
        $result = $this->wrapResult(parent::render($priceType, $object, $arguments));

        try {
            /** @var MsrpPrice $msrpPriceType */
            $msrpPriceType = $object->getPriceInfo()->getPrice('msrp');
        } catch (\InvalidArgumentException $e) {
            $this->_logger->logException($e);
            return $result;
        }
        if ($msrpPriceType->isMsrpEnabled()) {
            /** @var PriceBox $msrpBlock */
            $msrpBlock = $this->getChildBlock('default.msrp');
            if ($msrpBlock instanceof PriceBox) {
                $arguments['real_price_html'] = $result;
                $result = $msrpBlock->render('msrp', $object, $arguments);

                return $this->wrapResult($result);
            }
        }

        return $result;
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
