<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pricing
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pricing;

use Magento\Pricing\Amount\AmountInterface;
use Magento\Pricing\Object\SaleableInterface;
use Magento\Pricing\Render\Layout;
use Magento\View\Element\Template;
use Magento\View\Element\AbstractBlock;
use Magento\Pricing\Price\PriceInterface;

/**
 * Base price render
 *
 * @method string getPriceRenderHandle()
 */
class Render extends AbstractBlock
{
    /**@#+
     * Zones where prices displaying can be configured
     */
    const ZONE_ITEM_VIEW = 'item_view';
    const ZONE_ITEM_LIST = 'item_list';
    const ZONE_ITEM_OPTION = 'item_option';
    const ZONE_SALES     = 'sales';
    const ZONE_EMAIL     = 'email';
    const ZONE_DEFAULT   = null;
    /**@#-*/

    /**
     * Default type renderer
     *
     * @var string
     */
    protected $defaultTypeRender = 'default';

    /**
     * Price layout
     *
     * @var Layout
     */
    protected $priceLayout;

    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param Layout $priceLayout
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Layout $priceLayout,
        array $data = []
    ) {
        $this->priceLayout = $priceLayout;
        parent::__construct($context, $data);
    }

    /**
     * Prepare layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->priceLayout->addHandle($this->getPriceRenderHandle());
        $this->priceLayout->loadLayout();
        return parent::_prepareLayout();
    }

    /**
     * Render price
     *
     * @param string $priceCode
     * @param SaleableInterface $saleableItem
     * @param array $arguments
     * @return string
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function render($priceCode, SaleableInterface $saleableItem, array $arguments = [])
    {
        $useArguments = array_replace($this->_data, $arguments);

        /** @var \Magento\Pricing\Render\RendererPool $rendererPool */
        $rendererPool = $this->priceLayout->getBlock('render.product.prices');
        if (!$rendererPool) {
            throw new \RuntimeException('Wrong Price Rendering layout configuration. Factory block is missed');
        }

        // obtain concrete Price Render
        $priceRender = $rendererPool->createPriceRender($priceCode, $saleableItem, $useArguments);
        if ($priceRender) {
            //@TODO PriceBoxRenderInterface does not contain toHtml() method
            $result = $priceRender->toHtml();
        } else {
            $result = '';
        }
        // return rendered output
        return $result;
    }

    /**
     * Render price amount
     *
     * @param AmountInterface $amount
     * @param PriceInterface $price
     * @param SaleableInterface $saleableItem
     * @param array $arguments
     * @return string
     * @throws \RuntimeException
     */
    public function renderAmount(
        AmountInterface $amount,
        PriceInterface $price,
        SaleableInterface $saleableItem = null,
        array $arguments = []
    ) {
        $useArguments = array_replace($this->_data, $arguments);

        /** @var \Magento\Pricing\Render\RendererPool $rendererPool */
        $rendererPool = $this->priceLayout->getBlock('render.product.prices');
        if (!$rendererPool) {
            throw new \RuntimeException('Wrong Price Rendering layout configuration. Factory block is missed');
        }

        // obtain concrete Amount Render
        $amountRender = $rendererPool->createAmountRender($amount, $saleableItem, $price, $useArguments);
        if ($amountRender) {
            $result = $amountRender->toHtml();
        } else {
            $result = '';
        }
        // return rendered output
        return $result;
    }
}
