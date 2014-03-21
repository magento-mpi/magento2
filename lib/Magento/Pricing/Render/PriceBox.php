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

class PriceBox extends Template implements PriceBoxRenderInterface
{
    /**
     * @var SaleableInterface
     */
    protected $product;

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
     * @var \Magento\Pricing\PriceInfoInterface
     */
    protected $priceInfo;

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
     * @param SaleableInterface $object
     * @param array $arguments
     * @return string
     */
    public function render($priceType, SaleableInterface $object, array $arguments = [])
    {
        $origArguments = $this->_data;
        // @todo probably use block vars instead
        $this->_data = array_replace($origArguments, $arguments);

        $this->product = $object;

        $this->priceInfo = $object->getPriceInfo();
        $this->price = $this->priceInfo->getPrice($priceType);

        $cssClasses[] = 'price-' . $priceType;
        $this->_data['css_classes'] = implode(' ', $cssClasses);

        if (!$this->getTemplate() && $this->defaultTemplate) {
            $this->setTemplate($this->defaultTemplate);
        }

        $childBlock = $this->getChildBlock('price.render');
        if ($childBlock instanceof PriceBoxRenderInterface) {
            $result = $childBlock->render($childBlock->getNameInLayout(), $object, $arguments);
        } else {
            // wrap with standard required container
            $result = '<div class="price-box ' . $this->_data['css_classes'] . '">' . $this->toHtml() . '</div>';
        }

        // restore original block arguments
        $this->_data = $origArguments;

        // return result
        return $result;
    }

    /**
     * (to use in templates only)
     *
     * @param float|array $amount
     * @param array $arguments
     * @return string
     */
    public function renderAmount($amount, array $arguments = [])
    {
        return $this->getAmountRender()->render($amount, $this->price, $this->product, $arguments);
    }

    /**
     * (to use in templates only)
     *
     * @return SaleableInterface
     */
    public function getProduct()
    {
        // @todo move to abstract pricing block
        return $this->product;
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
     * @param $priceCode
     * @return PriceInterface
     */
    public function getPriceType($priceCode)
    {
        return $this->priceInfo->getPrice($priceCode);
    }

    /**
     * @param float $amount
     * @return float
     */
    public function convertToDisplayCurrency($amount)
    {
        // @todo move to abstract pricing block
        return $amount;
    }

    /**
     * @return string
     */
    public function getDisplayCurrencySymbol()
    {
        // @todo move to abstract pricing block
        return '';
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
                $this->hasData('amount_render_data') ? $this->getData('amount_render') : []
            );
        }
        return $this->amountRender;
    }
}
