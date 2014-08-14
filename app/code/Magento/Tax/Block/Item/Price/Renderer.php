<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Block\Item\Price;

use Magento\Tax\Helper\Data as TaxHelper;
use Magento\Framework\View\Element\Template\Context;

/**
 * Item price render block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Renderer extends \Magento\Checkout\Block\Item\Price\Renderer
{
    /**
     * @var \Magento\Tax\Helper\Data
     */
    protected $taxHelper;

    /**
     * Constructor
     *
     * @param Context $context
     * @param TaxHelper $taxHelper
     * @param array $data
     */
    public function __construct(Context $context, TaxHelper $taxHelper, array $data = array())
    {
        $this->taxHelper = $taxHelper;
        parent::__construct($context, $data);
    }

    /**
     * Return whether display setting is to display price including tax
     *
     * @return bool
     */
    public function displayPriceInclTax()
    {
        return $this->taxHelper->displayCartPriceInclTax();
    }

    /**
     * Return whether display setting is to display price excluding tax
     *
     * @return bool
     */
    public function displayPriceExclTax()
    {
        return $this->taxHelper->displayCartPriceExclTax();
    }

    /**
     * Return whether display setting is to display both price including tax and price excluding tax
     *
     * @return bool
     */
    public function displayBothPrices()
    {
        return $this->taxHelper->displayCartBothPrices();
    }
}
