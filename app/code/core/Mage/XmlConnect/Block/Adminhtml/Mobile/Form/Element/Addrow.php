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
 * Xmlconnect Add row form element
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Form_Element_Addrow
    extends Varien_Data_Form_Element_Button
{
    /**
     * Render Element Html
     *
     * @return string
     */
    public function getElementHtml()
    {
        $html = $this->getBeforeElementHtml()
            . '<button id="'.$this->getHtmlId()
            . '" name="'
            . $this->getName()
            . '" value="'.$this->getEscapedValue()
            . '" '
            . $this->serialize($this->getHtmlAttributes())
            . ' ><span><span><span>'
            . $this->getEscapedValue()
            . '</span></span></span></button>'
            . $this->getAfterElementHtml();
        return $html;
    }

    /**
     * Getter for "before_element_html"
     *
     * @return string
     */
    public function getBeforeElementHtml()
    {
        return $this->getData('before_element_html');
    }

    /**
     * Return label html code
     *
     * @param string $idSuffix
     * @return string
     */
    public function getLabelHtml($idSuffix = '')
    {
        if ($this->getLabel() !== null) {
            $html = '<label  for="' . $this->getHtmlId() . $idSuffix . '">'
                . $this->getLabel()
                . ($this->getRequired() ? ' <span class="required">*</span>' : '')
                . '</label>';
        } else {
            $html = '';
        }
        return $html;
    }

    /**
     * Overriding toHtml parent method
     * Adding addrow Block to element renderer
     *
     * @return string
     */
    public function toHtml()
    {
        $blockClassName = Mage::getConfig()->getBlockClassName('Mage_Adminhtml_Block_Template');
        //TODO: Get rid from Mage::getObjectManager
        $jsBlock = Mage::getObjectManager()->create($blockClassName);
        $jsBlock->setTemplate('Mage_XmlConnect::form/element/addrow.phtml');
        $jsBlock->setOptions($this->getOptions());
        return parent::toHtml() . $jsBlock->toHtml();
    }
}
