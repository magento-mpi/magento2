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
 * Form element button
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Button extends Magento_Data_Form_Element_Abstract
{
    /**
     * Additional html attributes
     *
     * @var array
     */
    protected $_htmlAttributes = array('data-mage-init');

    /**
     * Generate button html
     *
     * @return string
     */
    public function getElementHtml()
    {
        $html = '';
        if ($this->getBeforeElementHtml()) {
            $html .= sprintf(
                '<label class="addbefore" for="%s">%s</label>', $this->getHtmlId(), $this->getBeforeElementHtml()
            );
        }
        $html .= sprintf(
            '<button id="%s" %s %s><span>%s</span></button>',
            $this->getHtmlId(),
            $this->_getUiId(),
            $this->serialize($this->getHtmlAttributes()),
            $this->getEscapedValue()
        );

        if ($this->getAfterElementHtml()) {
            $html .= sprintf(
                '<label class="addafter" for="%s">%s</label>', $this->getHtmlId(), $this->getBeforeElementHtml()
            );
        }
        return $html;
    }

    /**
     * Html attributes
     *
     * @return array
     */
    public function getHtmlAttributes()
    {
        $attributes = parent::getHtmlAttributes();
        return array_merge($attributes, $this->_htmlAttributes);
    }
}
