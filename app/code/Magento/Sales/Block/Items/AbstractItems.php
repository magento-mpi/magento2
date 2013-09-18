<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract block for display sales (quote/order/invoice etc.) items
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Block\Items;

class AbstractItems extends \Magento\Core\Block\Template
{
    /**
     * Block alias fallback
     */
    const DEFAULT_TYPE = 'default';

    /**
     * Initialize default item renderer
     */
    protected function _prepareLayout()
    {
        if (!$this->getChildBlock(self::DEFAULT_TYPE)) {
            $this->addChild(
                self::DEFAULT_TYPE,
                'Magento_Checkout_Block_Cart_Item_Renderer',
                array('template' => 'cart/item/default.phtml')
            );
        }
        return parent::_prepareLayout();
    }

    /**
     * Retrieve item renderer block
     *
     * @param string $type
     * @return \Magento\Core\Block\AbstractBlock
     * @throws RuntimeException
     */
    public function getItemRenderer($type)
    {
        $renderer = $this->getChildBlock($type) ?: $this->getChildBlock(self::DEFAULT_TYPE);
        if (!$renderer instanceof Magento_Core_Block) {
            throw new RuntimeException('Renderer for type "' . $type . '" does not exist.');
        }
        $renderer->setRenderedBlock($this);
        return $renderer;
    }

    /**
     * Prepare item before output
     *
     * @param \Magento\Core\Block\AbstractBlock $renderer
     * @return \Magento\Sales\Block\Items\AbstractItems
     */
    protected function _prepareItem(\Magento\Core\Block\AbstractBlock $renderer)
    {
        return $this;
    }

    /**
     * Return product type for quote/order item
     *
     * @param \Magento\Object $item
     * @return string
     */
    protected function _getItemType(\Magento\Object $item)
    {
        if ($item->getOrderItem()) {
            $type = $item->getOrderItem()->getProductType();
        } elseif ($item instanceof \Magento\Sales\Model\Quote\Address\Item) {
            $type = $item->getQuoteItem()->getProductType();
        } else {
            $type = $item->getProductType();
        }
        return $type;
    }

    /**
     * Get item row html
     *
     * @param   \Magento\Object $item
     * @return  string
     */
    public function getItemHtml(\Magento\Object $item)
    {
        $type = $this->_getItemType($item);

        $block = $this->getItemRenderer($type)
            ->setItem($item);
        $this->_prepareItem($block);
        return $block->toHtml();
    }
}
