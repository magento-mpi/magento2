<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomAttribute
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * EAV entity Attribute Form Renderer Block for Multiply line
 *
 * @category    Magento
 * @package     Magento_CustomAttribute
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CustomAttribute_Block_Form_Renderer_Multiline extends Magento_CustomAttribute_Block_Form_Renderer_Abstract
{
    /**
     * Return original entity value
     * Value didn't escape and filter
     *
     * @return array
     */
    public function getValues()
    {
        $value = $this->getEntity()->getData($this->getAttributeObject()->getAttributeCode());
        if (!is_array($value)) {
            $value = explode("\n", $value);
        }
        return $value;
    }

    /**
     * Return count of lines for multiply line attribute
     *
     * @return int
     */
    public function getLineCount()
    {
        return $this->getAttributeObject()->getMultilineCount();
    }

    /**
     * Return array of validate classes
     *
     * @param boolean $withRequired
     * @return array
     */
    protected function _getValidateClasses($withRequired = true)
    {
        $classes    = parent::_getValidateClasses($withRequired);
        $rules      = $this->getAttributeObject()->getValidateRules();
        if (!empty($rules['min_text_length'])) {
            $classes[] = 'validate-length';
            $classes[] = 'minimum-length-' . $rules['min_text_length'];
        }
        if (!empty($rules['max_text_length'])) {
            if (!in_array('validate-length', $classes)) {
                $classes[] = 'validate-length';
            }
            $classes[] = 'maximum-length-' . $rules['max_text_length'];
        }

        return $classes;
    }

    /**
     * Return HTML class attribute value
     * Validate and rules
     *
     * @return string
     */
    public function getLineHtmlClass()
    {
        $classes = $this->_getValidateClasses(false);
        return empty($classes) ? '' : ' ' . implode(' ', $classes);
    }

    /**
     * Return filtered and escaped value
     *
     * @param int $index
     * @return string
     */
    public function getEscapedValue($index)
    {
        $values = $this->getValues();
        if (isset($values[$index])) {
            $value = $values[$index];
        } else {
            $value = '';
        }

        return $this->escapeHtml($this->_applyOutputFilter($value));
    }
}
