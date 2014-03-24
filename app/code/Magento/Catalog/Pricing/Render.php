<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Pricing;

use Magento\Pricing\Object\SaleableInterface;
use Magento\View\Element\Template;
use Magento\Registry;
use Magento\Pricing\Render as PricingRender;

/**
 * Catalog Price Render
 */
class Render extends Template
{
    /**
     * @var \Magento\Registry $registry
     */
    protected $registry;

    /**
     * Construct
     *
     * @param Template\Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Registry $registry,
        array $data = []
    ) {
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Produce and return block's html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        $parentBlock = $this->getParentBlock();
        $product = $parentBlock && $parentBlock->getProductItem()
            ? $parentBlock->getProductItem()
            : $this->registry->registry('product');

        /** @var PricingRender $priceRender */
        $priceRender = $this->getLayout()->getBlock($this->getPriceRender());
        if ($priceRender instanceof PricingRender) {
            /** @var SaleableInterface $product */
            if ($product instanceof SaleableInterface) {
                return $priceRender->render($this->getPriceTypeCode(), $product, $this->getData());
            }
        }
        return parent::_toHtml();
    }
}
