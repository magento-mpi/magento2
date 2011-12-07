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
 * Tab design accordion xml renderer
 *
 * @category     Mage
 * @package      Mage_Xmlconnect
 * @author       Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_Design_Accordion
    extends Mage_Adminhtml_Block_Widget_Accordion
{
    /**
     * Add accordion item by specified block
     *
     * @param string $itemId
     * @param mixed $block
     */
    public function addAccordionItem($itemId, $block)
    {
        if (strpos($block, '/') !== false) {
            $block = $this->getLayout()->createBlock($block);
        } else {
            $block = $this->getLayout()->getBlock($block);
        }

        $this->addItem($itemId, array(
            'title'   => $block->getTitle(),
            'content' => $block->toHtml(),
            'open'    => $block->getIsOpen(),
        ));
    }
}
