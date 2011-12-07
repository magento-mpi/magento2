<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Base html block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Core_Block_Text_List extends Mage_Core_Block_Text
{
    protected function _toHtml()
    {
        $this->setText('');
        foreach ($this->getSortedChildren() as $name) {
            $block = $this->getLayout()->getBlock($name);
            if (!$block) {
                Mage::throwException(Mage::helper('Mage_Core_Helper_Data')->__('Invalid block: %s', $name));
            }
            $this->addText($block->toHtml());
        }
        return parent::_toHtml();
    }
}
