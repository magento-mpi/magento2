<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Block\Adminhtml\Order\Create\Items\Price;

use Magento\Sales\Model\Quote\Item\AstractItem;

/**
 * Item price renderer for adminhtml sales order create items grid block
 */
class Renderer extends \Magento\Framework\View\Element\Template
{
    /**
     * @var AstractItem
     */
    protected $item;

    /**
     * Set item associated with this block
     *
     * @param Abstract $item
     */
    public function setItem($item)
    {
        $this->item = $item;
    }

    /**
     * Get item associated with this block
     *
     * @return AstractItem
     */
    public function getItem()
    {
        return $this->item;
    }

}
