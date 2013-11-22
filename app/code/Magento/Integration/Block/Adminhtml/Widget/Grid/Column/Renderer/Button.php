<?php
/**
 * Render HTML <button> tag.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Block\Adminhtml\Widget\Grid\Column\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use \Magento\Object;

class Button extends AbstractRenderer
{
    /** @var array Contains list of element's attributes */
    protected $_attributes = [];

    /**
     * {@inheritDoc}
     */
    public function render(Object $row)
    {
        $this->_prepareAttributes($row);
        return sprintf('<button %s>%s</button>', $this->_getAttributesStr(), $this->_getValue($row));
    }

    /**
     * Whether current item is disabled.
     *
     * @param \Magento\Object $row
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _isDisabled(Object $row)
    {
        return false;
    }

    /**
     * @param \Magento\Object $row
     * @return string
     */
    protected function _getDisabledAttribute(Object $row)
    {
        return $this->_isDisabled($row) ? 'disabled' : '';
    }

    /**
     * Prepare attribute list. Values for attributes gathered from two sources:
     * - If getter method exists in the class - it is taken from there (getter method for "title" attribute will
     *   be "_getTitleAttribute", for "onmouseup" - "_getOnmouseupAttribute" and so on)
     * - Then it tries to get it from column's layout description
     * If received attribute value is empty - attribute is not added to final HTML.
     *
     * @param \Magento\Object $row
     */
    protected function _prepareAttributes(Object $row)
    {
        foreach ($this->_getValidAttributes() as $attributeName) {
            $methodName = sprintf('_get%sAttribute', ucfirst($attributeName));
            $rowMethodName = sprintf('get%s', ucfirst($attributeName));
            $attributeValue = method_exists($this, $methodName)
                ? $this->$methodName($row)
                : $this->getColumn()->$rowMethodName();

            if ($attributeValue) {
                $this->_addAttribute($attributeName, $attributeValue);
            }
        }
    }

    /**
     * Add attribute to the list of element attributes.
     *
     * @param string $name  Attribute name, i.e. 'title', 'name', etc.
     * @param string $value Attribute value
     */
    protected function _addAttribute($name, $value)
    {
        $this->_attributes[] = sprintf('%s="%s"', $name, $this->escapeHtml($value));
    }

    /**
     * Get list of available HTML attributes for this element.
     *
     * @return array
     */
    protected function _getValidAttributes()
    {
        return [
            // HTML global attributes
            'accesskey', 'class', 'id', 'lang', 'style', 'tabindex', 'title',
            // HTML mouse event attributes
            'onclick', 'ondblclick', 'onmousedown', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup',
            // Element attributes
            'disabled', 'name', 'type', 'value',
        ];
    }

    /**
     * Get list of attributes rendered as a string (ready to be inserted into tag).
     *
     * @return string
     */
    protected function _getAttributesStr()
    {
        return join(' ', $this->_attributes);
    }
}
