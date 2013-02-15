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
 * Form element renderer to display color picker element for VDE
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_ColorPicker extends Varien_Data_Form_Element_Abstract
{
    /**
     * Get color-picker element HTML
     *
     * @return string
     */
    public function getElementHtml()
    {
        //@TODO Find a way to use external renderer here instead of hard-coded HTML inside

        $beforeElementHtml = $this->getBeforeElementHtml();
        $before = '';
        if ($beforeElementHtml) {
            $before = sprintf('<label class="addbefore" for="%s">%s</label>',
                $this->getHtmlId(),
                $this->getBeforeElementHtml()
            );
        }

        $after = '';
        $afterElementHtml = $this->getAfterElementHtml();
        if ($afterElementHtml) {
            $after = sprintf('<label class="addafter" for="%s">%s</label>',
                $this->getHtmlId(),
                $this->getAfterElementHtml()
            );
        }

        $marker = sprintf('<div style="width:20px; height:20px; background-color:%s; float: left; margin-right:10px; border:1px solid black;" onclick="%s"></div>',
            $this->getValue(),
            "alert('Not implemented yet')"
        );
        $style = 'style="float:left; width:60px;"';
        $clearer = '<div style="clear:both"></div>';

        $html = sprintf('%s%s<input id="%s" name="%s" %s value="%s" %s %s />%s%s',
            $before,
            $marker,
            $this->getHtmlId(),
            $this->getName(),
            $this->_getUiId(),
            $this->getEscapedValue(),
            $this->serialize($this->getHtmlAttributes()),
            $style,
            $clearer,
            $after
        );

        return $html;
    }
}
