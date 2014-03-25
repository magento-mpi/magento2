<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pricing
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pricing\Render;

use Magento\Pricing\Object\SaleableInterface;
use Magento\Pricing\Price\PriceInterface;
use Magento\View\Element\Template;

/**
 * Default price box renderer
 *
 * @method bool hasListClass()
 * @method string getListClass()
 */
class PriceBox extends Template implements PriceBoxRenderInterface
{
    /**
     * @var SaleableInterface
     */
    protected $saleableItem;

    /**
     * @var PriceInterface
     */
    protected $price;

    /**
     * @var string
     */
    protected $defaultTemplate = '';

    /**
     * @var \Magento\Pricing\Render\Amount
     */
    protected $amountRender;

    /**
     * @var AmountRenderFactory
     */
    protected $amountRenderFactory;

    /**
     * @param Template\Context $context
     * @param AmountRenderFactory $amountRenderFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        AmountRenderFactory $amountRenderFactory,
        array $data = array()
    ) {
        $this->amountRenderFactory = $amountRenderFactory;
        parent::__construct($context, $data);
    }

    /**
     * @param string $priceType
     * @param SaleableInterface $saleableItem
     * @param array $arguments
     * @return string
     */
    public function render($priceType, SaleableInterface $saleableItem, array $arguments = [])
    {
        $origArguments = $this->getData();
        // @todo probably use block vars instead
        $this->setData(array_replace($origArguments, $arguments));

        $this->saleableItem = $saleableItem;
        $this->price = $saleableItem->getPriceInfo()->getPrice($priceType);

        $cssClasses = explode(' ', $this->getData('css_classes'));
        $cssClasses[] = 'price-' . $priceType;
        $this->setData('css_classes', implode(' ', $cssClasses));

        if (!$this->getTemplate() && $this->defaultTemplate) {
            $this->setTemplate($this->defaultTemplate);
        }

        $result = $this->toHtml();
        // restore original block arguments after toHtml
        $this->setData($origArguments);
        return $result;
    }

    /**
     * (to use in templates only)
     *
     * @param PriceInterface $price
     * @param array $arguments
     * @return string
     */
    public function renderAmount(PriceInterface $price, array $arguments = [])
    {
        return $this->getAmountRender()->render($price, $this->saleableItem, $arguments);
    }

    /**
     * (to use in templates only)
     *
     * @return SaleableInterface
     */
    public function getSaleableItem()
    {
        // @todo move to abstract pricing block
        return $this->saleableItem;
    }

    /**
     * (to use in templates only)
     *
     * @return PriceInterface
     */
    public function getPrice()
    {
        // @todo move to abstract pricing block
        return $this->price;
    }

    /**
     * @param string $priceCode
     * @param float|null $quantity
     * @return PriceInterface
     */
    public function getPriceType($priceCode, $quantity = null)
    {
        return $this->saleableItem->getPriceInfo()->getPrice($priceCode, $quantity);
    }

    /**
     * @return Amount|\Magento\View\Element\BlockInterface
     */
    protected function getAmountRender()
    {
        if (!$this->amountRender) {
            $rendererClass = AmountRenderFactory::AMOUNT_RENDERER_DEFAULT;
            if ($this->hasData('amount_render')) {
                $rendererClass = $this->getData('amount_render');
            }
            $this->amountRender = $this->amountRenderFactory->create(
                $this->_layout,
                $rendererClass,
                $this->getData('amount_render_template'),
                $this->hasData('amount_render_data') ? $this->getData('amount_render_data') : []
            );
        }
        return $this->amountRender;
    }
}
