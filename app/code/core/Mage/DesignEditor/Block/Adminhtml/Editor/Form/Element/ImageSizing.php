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
 * Form element renderer to display image sizing element for VDE
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_ImageSizing extends Varien_Data_Form_Element_Text
{
    /**
     * Render HTML for element
     *
     * @return string
     */
    public function getElementHtml()
    {
        $html = '<span>%s</span>'
            . '<span><input id="%s" name="%s" %s value="%s"/></span>'
            . '<span><input id="%s" name="%s" %s value="%s"/></span>'
            . '<span><a data-mage-init="%s" href="#">%s</a></span>';

        $html = sprintf($html,
            $this->_getImageTypeSelect($this->getValue('type')),
            $this->getHtmlId() . '_width',
            $this->getName() . '[width]',
            $this->_getUiId('width'),
            $this->getEscapedValue('width'),
            $this->getHtmlId() . '_height',
            $this->getName() . '[height]',
            $this->_getUiId('height'),
            $this->getEscapedValue('height'),
            $this->getDefaultValuesEvent(),
            $this->getDefaultValuesLabel()
        );
        return $html;
    }

    /**
     * @param string $value
     * @return string
     */
    protected function _getImageTypeSelect($value = null)
    {
        $select = new Varien_Data_Form_Element_Select();
        $select->setId($this->getHtmlId() . '_type');
        $select->setValues($this->getSelectOptions());
        $select->setForm( $this->getForm());
        $select->setValue($value);
        return $select->getElementHtml();
    }
}

