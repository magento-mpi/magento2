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
class Magento_Sales_Block_Items_Abstract extends Magento_Core_Block_Template
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
     * @return Magento_Core_Block_Abstract
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
     * @param Magento_Core_Block_Abstract $renderer
     * @return Magento_Sales_Block_Items_Abstract
     */
    protected function _prepareItem(Magento_Core_Block_Abstract $renderer)
    {
        return $this;
    }

    /**
     * Return product type for quote/order item
     *
     * @param Magento_Object $item
     * @return string
     */
    protected function _getItemType(Magento_Object $item)
    {
        if ($item->getOrderItem()) {
            $type = $item->getOrderItem()->getProductType();
        } elseif ($item instanceof Magento_Sales_Model_Quote_Address_Item) {
            $type = $item->getQuoteItem()->getProductType();
        } else {
            $type = $item->getProductType();
        }
        return $type;
    }

    /**
     * Get item row html
     *
     * @param   Magento_Object $item
     * @return  string
     */
    public function getItemHtml(Magento_Object $item)
    {
        $type = $this->_getItemType($item);

        $block = $this->getItemRenderer($type)
            ->setItem($item);
        $this->_prepareItem($block);
        return $block->toHtml();
    }
}
