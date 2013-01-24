<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Substitution for regular head block
 */
class Mage_DesignEditor_Block_Page_Html_Head extends Mage_Page_Block_Html_Head
{
    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'Mage_Page::html/head.phtml';

    /**
     * Render HTML for the added head items
     *
     * @return string
     */
    public function getCssJsHtml()
    {
        /** @var $block Mage_DesignEditor_Block_Page_Html_Head_Vde */
        $block = $this->getLayout()->getBlock('vde_head');
        if ($block) {
            // remove all current JS files
            foreach (array_keys($this->_data['items']) as $itemKey) {
                if (strpos($itemKey, 'js/') === 0) {
                    unset($this->_data['items'][$itemKey]);
                }
            }

            // add data from VDE head
            $vdeItems = $block->getData('items');
            $this->_data['items'] = array_merge($this->_data['items'], $vdeItems);
        }

        return parent::getCssJsHtml();
    }
}
