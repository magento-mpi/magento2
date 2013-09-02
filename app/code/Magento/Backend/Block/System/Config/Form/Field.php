<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract config form element renderer
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Magento_Backend_Block_System_Config_Form_Field
    extends Magento_Backend_Block_Template
    implements Magento_Data_Form_Element_Renderer_Interface
{
    /**
     * Application
     *
     * @var Magento_Core_Model_App
     */
    protected $_application;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_App $application
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_App $application,
        array $data = array()
    ) {
        $this->_application = $application;
        parent::__construct($context, $coreStoreConfig, $data);
    }

    /**
     * Retrieve element HTML markup
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Magento_Data_Form_Element_Abstract $element)
    {
        return $element->getElementHtml();
    }

    /**
     * Retrieve HTML markup for given form element
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Magento_Data_Form_Element_Abstract $element)
    {
        $isCheckboxRequired = $this->_isInheritCheckboxRequired($element);

        // Disable element if value is inherited from other scope. Flag has to be set before the value is rendered.
        if ($element->getInherit() == 1 && $isCheckboxRequired) {
            $element->setDisabled(true);
        }

        $html = '<td class="label"><label for="' . $element->getHtmlId() . '">'
            . $element->getLabel() . '</label></td>';
        $html .= $this->_renderValue($element);

        if ($isCheckboxRequired) {
            $html .= $this->_renderInheritCheckbox($element);
        }

        $html .= $this->_renderScopeLabel($element);
        $html .= $this->_renderHint($element);

        return $this->_decorateRowHtml($element, $html);
    }

    /**
     * Render element value
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _renderValue(Magento_Data_Form_Element_Abstract $element)
    {
        if ($element->getTooltip()) {
            $html = '<td class="value with-tooltip">';
            $html .= $this->_getElementHtml($element);
            $html .= '<div class="tooltip"><span class="help"><span></span></span>';
            $html .= '<div class="tooltip-content">' . $element->getTooltip() . '</div></div>';
        } else {
            $html = '<td class="value">';
            $html .= $this->_getElementHtml($element);
        }
        if ($element->getComment()) {
            $html .= '<p class="note"><span>' . $element->getComment() . '</span></p>';
        }
        $html .= '</td>';
        return $html;
    }

    /**
     * Render inheritance checkbox (Use Default or Use Website)
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _renderInheritCheckbox(Magento_Data_Form_Element_Abstract $element)
    {
        $htmlId = $element->getHtmlId();
        $namePrefix = preg_replace('#\[value\](\[\])?$#', '', $element->getName());
        $checkedHtml = ($element->getInherit() == 1) ? 'checked="checked"' : '';

        $html = '<td class="use-default">';
        $html .= '<input id="' . $htmlId . '_inherit" name="' . $namePrefix . '[inherit]" type="checkbox" value="1"'
            . ' class="checkbox config-inherit" ' . $checkedHtml
            . ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" /> ';
        $html .= '<label for="' . $htmlId . '_inherit" class="inherit">' . $this->_getInheritCheckboxLabel($element)
            . '</label>';
        $html .= '</td>';

        return $html;
    }

    /**
     * Check if inheritance checkbox has to be rendered
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @return bool
     */
    protected function _isInheritCheckboxRequired(Magento_Data_Form_Element_Abstract $element)
    {
        return $element->getCanUseWebsiteValue() || $element->getCanUseDefaultValue();
    }

    /**
     * Retrieve label for the inheritance checkbox
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getInheritCheckboxLabel(Magento_Data_Form_Element_Abstract $element)
    {
        $checkboxLabel = __('Use Default');
        if ($element->getCanUseWebsiteValue()) {
            $checkboxLabel =  __('Use Website');
        }
        return $checkboxLabel;
    }

    /**
     * Render scope label
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _renderScopeLabel(Magento_Data_Form_Element_Abstract $element)
    {
        $html = '<td class="scope-label">';
        if ($element->getScope() && false == $this->_application->isSingleStoreMode()) {
            $html .= $element->getScopeLabel();
        }
        $html .= '</td>';
        return $html;
    }

    /**
     * Render field hint
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _renderHint(Magento_Data_Form_Element_Abstract $element)
    {
        $html = '<td class="">';
        if ($element->getHint()) {
            $html .= '<div class="hint"><div style="display: none;">' . $element->getHint() . '</div></div>';
        }
        $html .= '</td>';
        return $html;
    }

    /**
     * Decorate field row html
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @param string $html
     * @return string
     */
    protected function _decorateRowHtml($element, $html)
    {
        return '<tr id="row_' . $element->getHtmlId() . '">' . $html . '</tr>';
    }
}
