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
use Magento\Pricing\Price\PriceInterface;
use Magento\Pricing\Render\PriceBox as PriceBoxRender;
use Magento\View\Element\Template\Context;
use Magento\Pricing\Render\RendererPool;
use Magento\Core\Helper\Data;
use Magento\Math\Random;

/**
 * Default catalog price box render
 *
 * @method string getPriceElementIdPrefix()
 * @method string getIdSuffix()
 * @method string getDisplayMsrpHelpMessage()
 */
class PriceBox extends PriceBoxRender
{
    /**
     * @var \Magento\Core\Helper\Data
     */
    protected $coreDataHelper;

    /**
     * @var \Magento\Math\Random
     */
    protected $mathRandom;

    /**
     * @param Context $context
     * @param SaleableInterface $saleableItem
     * @param PriceInterface $price
     * @param RendererPool $rendererPool
     * @param Data $coreDataHelper
     * @param Random $mathRandom
     * @param array $data
     */
    public function __construct(
        Context $context,
        SaleableInterface $saleableItem,
        PriceInterface $price,
        RendererPool $rendererPool,
        Data $coreDataHelper,
        Random $mathRandom,
        array $data = array()
    ) {
        $this->coreDataHelper = $coreDataHelper;
        $this->mathRandom = $mathRandom;
        parent::__construct($context, $saleableItem, $price, $rendererPool);
    }

    /**
     * Encode the mixed $valueToEncode into the JSON format
     *
     * @param mixed $valueToEncode
     * @param boolean $cycleCheck Optional; whether or not to check for object recursion; off by default
     * @param array $options Additional options used during encoding
     * @return string
     */
    public function jsonEncode($valueToEncode, $cycleCheck = false, $options = [])
    {
        return $this->coreDataHelper->jsonEncode($valueToEncode, $cycleCheck, $options);
    }

    /**
     * Get random string
     *
     * @param int $length
     * @param string|null $chars
     * @return string
     */
    public function getRandomString($length, $chars = null)
    {
        return $this->mathRandom->getRandomString($length, $chars);
    }

    /**
     * Check if quantity can be displayed for tier price with msrp
     *
     * @param SaleableInterface $product
     * @return bool
     */
    public function getCanDisplayQty(SaleableInterface $product)
    {
        if ($product->getTypeId() == \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE) {
            return false;
        }
        return true;
    }
}
