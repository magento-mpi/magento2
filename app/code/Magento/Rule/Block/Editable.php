<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rule
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Rule_Block_Editable
    extends Magento_Core_Block_Abstract
    implements Magento_Data_Form_Element_Renderer_Interface
{
    /**
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData = null;

    /**
     * Core string
     *
     * @var Magento_Core_Helper_String
     */
    protected $_coreString = null;

    /**
     * @param Magento_Core_Helper_String $coreString
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_String $coreString,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Context $context,
        array $data = array()
    ) {
        $this->_coreString = $coreString;
        $this->_coreData = $coreData;
        parent::__construct($context, $data);
    }

    /**
     * Render element
     *
     * @see Magento_Data_Form_Element_Renderer_Interface::render()
     * @param Magento_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Magento_Data_Form_Element_Abstract $element)
    {
        $element->addClass('element-value-changer');
        $valueName = $element->getValueName();

        if ($valueName === '') {
            $valueName = '...';
        }

        $coreHelper = $this->_coreData;
        $stringHelper = $this->_coreString;

        if ($element->getShowAsText()) {
            $html = ' <input type="hidden" class="hidden" id="' . $element->getHtmlId()
                . '" name="' . $element->getName() . '" value="' . $element->getValue() . '"/> '
                . htmlspecialchars($valueName) . '&nbsp;';
        } else {
            $html = ' <span class="rule-param"'
                . ($element->getParamId() ? ' id="' . $element->getParamId() . '"' : '') . '>'
                . '<a href="javascript:void(0)" class="label">';

            if ($this->_translator->isAllowed()) {
                $html .= $coreHelper->escapeHtml($valueName);
            } else {
                $html .= $coreHelper->escapeHtml($stringHelper->truncate($valueName, 33, '...'));
            }

            $html .= '</a><span class="element"> ' . $element->getElementHtml();

            if ($element->getExplicitApply()) {
                $html .= ' <a href="javascript:void(0)" class="rule-param-apply"><img src="'
                    . $this->getViewFileUrl('images/rule_component_apply.gif') . '" class="v-middle" alt="'
                    . __('Apply') . '" title="' . __('Apply') . '" /></a> ';
            }

            $html .= '</span></span>&nbsp;';
        }

        return $html;
    }
}
