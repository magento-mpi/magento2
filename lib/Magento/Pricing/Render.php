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

use Magento\Pricing\Object\SaleableInterface;
use Magento\Pricing\Render\Layout;
use Magento\View\Element\Template;
use Magento\View\Element\AbstractBlock;

/**
 * Base price render
 *
 * @method string getPriceRenderHandle()
 */
class Render extends AbstractBlock
{
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
     * @param float $amount
     * @param string $priceCode
     * @param SaleableInterface $saleableItem
     * @param array $arguments
     * @return string
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function renderAmount($amount, $priceCode = '', SaleableInterface $saleableItem = null, array $arguments = [])
    {
        $useArguments = array_replace($this->_data, $arguments);

        /** @var \Magento\Pricing\Render\RendererPool $rendererPool */
        $rendererPool = $this->priceLayout->getBlock('render.product.prices');
        if (!$rendererPool) {
            throw new \RuntimeException('Wrong Price Rendering layout configuration. Factory block is missed');
        }

        // obtain concrete Amount Render
        $amountRender = $rendererPool->createAmountRender($amount, $priceCode, $saleableItem, $useArguments);
        if ($amountRender) {
            $result = $amountRender->toHtml();
        } else {
            $result = '';
        }
        // return rendered output
        return $result;
    }
}
