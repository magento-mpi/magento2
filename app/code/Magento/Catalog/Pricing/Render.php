<?php
namespace Magento\Catalog\Pricing;

use Magento\Pricing\Object\SaleableInterface;
use Magento\View\Element\Template;
use Magento\Registry;
use Magento\Pricing\Render as PricingRender;

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
        /** @var PricingRender $priceRender */
        $priceRender = $this->getLayout()->getBlock($this->getPriceRender());
        if ($priceRender instanceof PricingRender) {
            /** @var SaleableInterface $product */
            $product = $this->registry->registry('product');
            if ($product instanceof SaleableInterface) {
                return $priceRender->render($this->getPriceTypeCode(), $product, []);
            }
        }

        return parent::_toHtml();
    }
}
