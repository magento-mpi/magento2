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

use Magento\Pricing\Amount\AmountInterface;
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
     * @var RendererPool
     */
    protected $rendererPool;

    /**
     * @param Template\Context  $context
     * @param SaleableInterface $saleableItem
     * @param PriceInterface    $price
     * @param RendererPool      $rendererPool
     * @param array             $data
     */
    public function __construct(
        Template\Context $context,
        SaleableInterface $saleableItem,
        PriceInterface $price,
        RendererPool $rendererPool,
        array $data = []
    ) {
        $this->saleableItem = $saleableItem;
        $this->price = $price;
        $this->rendererPool = $rendererPool;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $cssClasses = $this->hasData('css_classes') ? explode(' ', $this->getData('css_classes')) : [];
        $cssClasses[] = 'price-' . $this->getPrice()->getPriceType();
        $this->setData('css_classes', implode(' ', $cssClasses));
        return parent::_toHtml();
    }

    /**
     * @return SaleableInterface
     */
    public function getSaleableItem()
    {
        // @todo move to abstract pricing block
        return $this->saleableItem;
    }

    /**
     * @return PriceInterface
     */
    public function getPrice()
    {
        // @todo move to abstract pricing block
        return $this->price;
    }

    /**
     * Get price id
     *
     * @param null|string $defaultPrefix
     * @param null|string $defaultSuffix
     * @return string
     */
    public function getPriceId($defaultPrefix = null, $defaultSuffix = null)
    {
        if ($this->hasData('price_id')) {
            return $this->getData('price_id');
        }
        $priceId = $this->saleableItem->getId();
        if ($this->hasData('price_id_prefix')) {
            $prefix = $this->getData('price_id_prefix') . $priceId;
        } else {
            $prefix = $defaultPrefix;
        }
        if ($this->hasData('price_id_suffix')) {
            $suffix = $this->getData('price_id_suffix');
        } else {
            $suffix = $defaultSuffix;
        }
        $priceId = $prefix . $priceId . $suffix;
        return $priceId;
    }

    /**
     * Retrieve price object of given type and quantity
     *
     * @param string $priceCode
     * @param float|null $quantity
     * @return PriceInterface
     */
    public function getPriceType($priceCode, $quantity = null)
    {
        return $this->saleableItem->getPriceInfo()->getPrice($priceCode, $quantity);
    }

    /**
     * @param AmountInterface $amount
     * @param array $arguments
     * @return string
     */
    public function renderAmount(AmountInterface $amount, array $arguments = [])
    {
        $arguments = array_replace($this->getData(), $arguments);

        //@TODO AmountInterface does not contain toHtml() method
        return $this->getAmountRender($amount, $arguments)->toHtml();
    }

    /**
     * @param AmountInterface $amount
     * @param array $arguments
     * @return AmountRenderInterface
     */
    protected function getAmountRender(AmountInterface $amount, array $arguments = [])
    {
        return $this->rendererPool->createAmountRender(
            $amount,
            $this->getSaleableItem(),
            $this->getPrice(),
            $arguments
        );
    }

    /**
     * @return RendererPool
     */
    public function getRendererPool()
    {
        return $this->rendererPool;
    }
}
