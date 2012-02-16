<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml block for showing product options fieldsets
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author    Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Composite_Fieldset extends Mage_Core_Block_Text_List
{
    /**
     *
     * Iterates through fieldsets and fetches complete html
     *
     * @return string
     */
    protected function _toHtml()
    {
        $children = $this->getSortedChildren();
        $total = count($children);
        $i = 0;
        $this->setText('');
        foreach ($children as $name) {
            $block = $this->getLayout()->getStructure()->getElementByName($name);
            if (!$block) {
                Mage::throwException(Mage::helper('Mage_Core_Helper_Data')->__('Invalid block: %s', $name));
            }
            if (!$this->getLayout()->getStructure()->isBlock($block)) {
                throw new Magento_Exception('Can not get HTML for container: ' . $name);
            }

            $i++;
            $block->setIsLastFieldset($i == $total);

            $this->addText($block->toHtml());
        }

        return parent::_toHtml();
    }
}
