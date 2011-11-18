<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer order view items xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Customer_Order_Items extends Mage_Sales_Block_Order_Items
{
    /**
     * Initialize default item renderer
     */
    protected function _construct()
    {
        parent::_construct();
        $this->addItemRender('default', 'Mage_XmlConnect_Block_Customer_Order_Item_Renderer_Default', null);
    }

    /**
     * Retrieve item renderer block
     *
     * @param string $type
     * @return Mage_Core_Block_Abstract
     */
    public function getItemRenderer($type)
    {
        if (empty($type) || !isset($this->_itemRenders[$type])) {
            $type = 'default';
        }

        if (is_null($this->_itemRenders[$type]['renderer'])) {
            $this->_itemRenders[$type]['renderer'] = $this->getLayout()
                ->createBlock($this->_itemRenders[$type]['block'])->setRenderedBlock($this);
        }
        return $this->_itemRenders[$type]['renderer'];
    }

    /**
     * Render XML for items
     * (get from template: order/items.phtml)
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $orderXmlObj
     * @return null
     */
    public function addItemsToXmlObject(Mage_XmlConnect_Model_Simplexml_Element $orderXmlObj)
    {
        $itemsXml = $orderXmlObj->addChild('ordered_items');

        foreach ($this->getItems() as $item) {
            if ($item->getParentItem()) {
                // if Item is option of grouped product - do not render it
                continue;
            }
            $type = $this->_getItemType($item);

            // TODO: take out all Enterprise renderers from layout update into array an realize checking of their using
            // Check if the Enterprise_GiftCard module is available for rendering
            if ($type == 'giftcard' && !is_object(Mage::getConfig()->getNode('modules/Enterprise_GiftCard'))) {
                continue;
            }
            $renderer = $this->getItemRenderer($type)->setItem($item);
            if (method_exists($renderer, 'addItemToXmlObject')) {
                $renderer->addItemToXmlObject($itemsXml);
            }
        }
    }
}
