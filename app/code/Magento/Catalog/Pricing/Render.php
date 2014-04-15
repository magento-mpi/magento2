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
 *
 * @method string getPriceRender()
 * @method string getPriceTypeCode()
 * @method string getDisplayMsrpHelpMessage()
 */
class Render extends Template
{
    /**
     * @var \Magento\Registry
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
        Registry $registry,
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
        /** @var PricingRender $priceRender */
        $priceRender = $this->getLayout()->getBlock($this->getPriceRender());
        if ($priceRender instanceof PricingRender) {
            $product = $this->getProduct();
            if ($product instanceof SaleableInterface) {
                return $priceRender->render($this->getPriceTypeCode(), $product, $this->getData());
            }
        }
        return parent::_toHtml();
    }

    /**
     * Returns saleable item instance
     *
     * @return SaleableInterface
     */
    protected function getProduct()
    {
        $parentBlock = $this->getParentBlock();

        $product = $parentBlock && $parentBlock->getProductItem()
            ? $parentBlock->getProductItem()
            : $this->registry->registry('product');
        return $product;
    }
}
