<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Block\Adminhtml\Widget\Grid\Column\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Integration\Model\Integration;
use Magento\Object;

/**
 * Render HTML <button> tag.
 *
 * @package Magento\Integration\Block\Adminhtml\Widget\Grid\Column\Renderer
 */
class Button extends AbstractRenderer
{
    /**
     * {@inheritdoc}
     */
    public function render(Object $row)
    {
        /** @var array $attributes */
        $attributes = $this->_prepareAttributes($row);
        return sprintf('<button %s>%s</button>', $this->_getAttributesStr($attributes), $this->_getValue($row));
    }

    /**
     * Determine whether current integration came from config file
     *
     * @param \Magento\Object $row
     * @return bool
     */
    protected function _isConfigBasedIntegration(Object $row)
    {
        return $row->hasData(
            Integration::SETUP_TYPE
        ) && $row->getData(
            Integration::SETUP_TYPE
        ) == Integration::TYPE_CONFIG;
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
     * - If getter method exists in the class - it is taken from there (getter method for "title"
     *   attribute will be "_getTitleAttribute", for "onmouseup" - "_getOnmouseupAttribute" and so on.)
     * - Then it tries to get it from the button's column layout description.
     * If received attribute value is empty - attribute is not added to final HTML.
     *
     * @param \Magento\Object $row
     * @return array
     */
    protected function _prepareAttributes(Object $row)
    {
        $attributes = array();
        foreach ($this->_getValidAttributes() as $attributeName) {
            $methodName = sprintf('_get%sAttribute', ucfirst($attributeName));
            $rowMethodName = sprintf('get%s', ucfirst($attributeName));
            $attributeValue = method_exists(
                $this,
                $methodName
            ) ? $this->{$methodName}(
                $row
            ) : $this->getColumn()->{$rowMethodName}();

            if ($attributeValue) {
                $attributes[] = sprintf('%s="%s"', $attributeName, $this->escapeHtml($attributeValue));
            }
        }
        return $attributes;
    }

    /**
     * Get list of available HTML attributes for this element.
     *
     * @return array
     */
    protected function _getValidAttributes()
    {
        /*
         * HTML global attributes - 'accesskey', 'class', 'id', 'lang', 'style', 'tabindex', 'title'
         * HTML mouse event attributes - 'onclick', 'ondblclick', 'onmousedown', 'onmousemove', 'onmouseout',
         *                               'onmouseover', 'onmouseup'
         * Element attributes - 'disabled', 'name', 'type', 'value'
         */
        return array(
            'accesskey',
            'class',
            'id',
            'lang',
            'style',
            'tabindex',
            'title',
            'onclick',
            'ondblclick',
            'onmousedown',
            'onmousemove',
            'onmouseout',
            'onmouseover',
            'onmouseup',
            'disabled',
            'name',
            'type',
            'value'
        );
    }

    /**
     * Get list of attributes rendered as a string (ready to be inserted into tag).
     *
     * @param array $attributes Array of attributes
     * @return string
     */
    protected function _getAttributesStr($attributes)
    {
        return join(' ', $attributes);
    }
}
