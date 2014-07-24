<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Block\Cart\Item\Price;

use Magento\Sales\Model\Quote\Item;
use Magento\Catalog\Pricing\Price\ConfiguredPriceInterface;
use Magento\Weee\Model\Tax;

/**
 * Item price render block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Renderer extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Item
     */
    protected $_item;

    /**
     * Set item for render
     *
     * @param \Magento\Sales\Model\Quote\Item\AbstractItem $item
     * @return $this
     */
    public function setItem(\Magento\Sales\Model\Quote\Item\AbstractItem $item)
    {
        $this->_item = $item;
        return $this;
    }

    /**
     * Get quote item
     *
     * @return Item
     */
    public function getItem()
    {
        return $this->_item;
    }
}
