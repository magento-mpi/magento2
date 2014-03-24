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

use Magento\Pricing\Render\PriceBox as PriceBoxRender;
use Magento\View\Element\Template\Context;
use Magento\Pricing\Render\AmountRenderFactory;
use Magento\Core\Helper\Data;

/**
 * Default catalog price box render
 */
class PriceBox extends PriceBoxRender
{
    /**
     * @var \Magento\Core\Helper\Data
     */
    protected $coreDataHelper;

    /**
     * @param Context $context
     * @param AmountRenderFactory $amountRenderFactory
     * @param Data $coreDataHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        AmountRenderFactory $amountRenderFactory,
        Data $coreDataHelper,
        array $data = array()
    ) {
        $this->coreDataHelper = $coreDataHelper;
        parent::__construct($context, $amountRenderFactory);
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
}
