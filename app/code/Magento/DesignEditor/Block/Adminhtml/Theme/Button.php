<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Button widget
 */
class Magento_DesignEditor_Block_Adminhtml_Theme_Button extends Magento_Core_Block_Template
{
    /**
     * Define block template
     */
    protected function _construct()
    {
        $this->setTemplate('Magento_DesignEditor::theme/button.phtml');
        parent::_construct();
    }

    /**
     * Retrieve attributes html
     *
     * @return string
     */
    public function getAttributesHtml()
    {
        $disabled = $this->getDisabled() ? 'disabled' : '';
        $title = $this->getTitle() ?: $this->getLabel();

        $classes = array();
        if ($this->getClass()) {
            $classes[] = $this->getClass();
        }
        if ($disabled) {
            $classes[] = $disabled;
        }

        return $this->_attributesToHtml($this->_prepareAttributes($title, $classes, $disabled));
    }

    /**
     * Prepare attributes
     *
     * @param string $title
     * @param array $classes
     * @param string $disabled
     * @return array
     */
    protected function _prepareAttributes($title, $classes, $disabled)
    {
        return array(
            'id'        => $this->getId(),
            'name'      => $this->getElementName(),
            'href'      => $this->getHref(),
            'title'     => $title,
            'class'     => implode(' ', $classes),
            'style'     => $this->getStyle(),
            'target'    => $this->getTarget(),
            'disabled'  => $disabled
        );
    }

    /**
     * Attributes list to html
     *
     * @param array $attributes
     * @return string
     */
    protected function _attributesToHtml($attributes)
    {
        $helper = $this->helper('Magento_Backend_Helper_Data');
        $html = '';
        foreach ($attributes as $attributeKey => $attributeValue) {
            if ($attributeValue !== null && $attributeValue !== '') {
                $html .= $attributeKey . '="' . $helper->escapeHtml($attributeValue) . '" ';
            }
        }
        return $html;
    }
}
