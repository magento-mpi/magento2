<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\DesignEditor\Block\Adminhtml\Theme;

/**
 * Button widget
 */
class Button extends \Magento\View\Element\Template
{
    /**
     * Define block template
     *
     * @return void
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
            'id' => $this->getId(),
            'name' => $this->getElementName(),
            'href' => $this->getHref(),
            'title' => $title,
            'class' => implode(' ', $classes),
            'style' => $this->getStyle(),
            'target' => $this->getTarget(),
            'disabled' => $disabled
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
        $html = '';
        foreach ($attributes as $attributeKey => $attributeValue) {
            if ($attributeValue !== null && $attributeValue !== '') {
                $html .= $attributeKey . '="' . $this->escapeHtml($attributeValue) . '" ';
            }
        }
        return $html;
    }
}
