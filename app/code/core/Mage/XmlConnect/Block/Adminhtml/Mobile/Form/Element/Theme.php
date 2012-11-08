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
 * XmlConnect theme form element
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Form_Element_Theme
    extends Varien_Data_Form_Element_Text
{
    /**
     * Generate themes (colors) html
     *
     * @return string
     */
    public function getHtml()
    {
        $blockClassName = Mage::getConfig()
            ->getBlockClassName('Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_Design_Themes');
        //TODO: Get rid from Mage::app
        $block = Mage::app()->getLayout()->createBlock($blockClassName);
        $block->setThemes($this->getThemes());
        $block->setName($this->getName());
        $block->setValue($this->getValue());
        return $block->toHtml();
    }
}
