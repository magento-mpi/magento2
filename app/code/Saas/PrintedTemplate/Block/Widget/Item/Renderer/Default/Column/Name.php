<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Base renderer for name field
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Blocks
 */
class Saas_PrintedTemplate_Block_Widget_Item_Renderer_Default_Column_Name
    extends Mage_Backend_Block_Abstract
    implements Saas_PrintedTemplate_Block_Widget_Item_Renderer_Default_Column_Abstract
{
    /**
     * Get value from option
     *
     * @param $option array Option
     * @return array
     */
    public function getOptionValue($option)
    {
        $values = array();

        if ($option['value']) {
            $_printValue = isset($option['print_value'])
                ? $option['print_value']
                : strip_tags($option['value']);
            $values = explode(', ', $_printValue);
        }

        return $values;
    }

    /**
     * Get item options
     *
     * @return array
     */
    public function getItemOptions()
    {
        $result = array();
        if ($options = $this->getItem()->getOrderItem()->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }

        return $result;
    }

    /**
     * Build item name HTML
     *
     * @return string
     */
    public function getHtml()
    {
        $result = '<div class="item-name">' . __($this->getItem()->getName()) . '</div>';
        foreach ($this->getItemOptions() as $option) {
            $result .= '<div class="item-option-label"><em>' . __($option['label']) . '</em></div>';
            foreach ($this->getOptionValue($option) as $value) {
                $result .= '<div class="item-option-value">' . __($value) . '</div>';
            }
        }

        return $result;
    }
}
