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
     * Renderers with render type key
     * block    => the block name
     * template => the template file
     * renderer => the block object
     *
     * @var array
     */
    protected $_itemRenders = array();

    /**
     * Initialize default item renderer
     */
    protected function _construct()
    {
        parent::_construct();
        $this->addItemRender('default', 'Magento\Checkout\Block\Cart\Item\Renderer', 'cart/item/default.phtml');
    }

    /**
     * Add renderer for item product type
     *
     * @param   string $type
     * @param   string $block
     * @param   string $template
     * @return  \Magento\Checkout\Block\Cart\AbstractCart
     */
    public function addItemRender($type, $block, $template)
    {
        $this->_itemRenders[$type] = array(
            'block'     => $block,
            'template'  => $template,
            'renderer'  => null
        );

        return $this;
    }

    /**
     * Retrieve item renderer block
     *
     * @param string $type
     * @return \Magento\Core\Block\AbstractBlock
     */
    public function getItemRenderer($type)
    {
        if (!isset($this->_itemRenders[$type])) {
            $type = 'default';
        }

        if (is_null($this->_itemRenders[$type]['renderer'])) {
            $this->_itemRenders[$type]['renderer'] = $this->getLayout()
                ->createBlock($this->_itemRenders[$type]['block'])
                ->setTemplate($this->_itemRenders[$type]['template'])
                ->setRenderedBlock($this);
        }
        return $this->_itemRenders[$type]['renderer'];
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
