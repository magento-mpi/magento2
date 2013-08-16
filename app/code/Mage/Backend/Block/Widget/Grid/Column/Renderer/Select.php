<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Grid select input column renderer
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Backend_Block_Widget_Grid_Column_Renderer_Select
    extends Mage_Backend_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @var Mage_Backend_Block_Widget_Grid_Column_Renderer_Options_Converter
     */
    protected $_converter;

    /**
     * @param Mage_Backend_Block_Context $context
     * @param Mage_Backend_Block_Widget_Grid_Column_Renderer_Options_Converter $converter
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Block_Context $context,
        Mage_Backend_Block_Widget_Grid_Column_Renderer_Options_Converter $converter,
        array $data = array()
    ) {
        $this->_converter = $converter;
        parent::__construct($context, $data);
    }

    /**
     * Get options from column
     *
     * @return array
     */
    protected function _getOptions()
    {
         return $this->_converter->toFlatArray($this->getColumn()->getOptions());
    }

    /**
     * Renders grid column
     *
     * @param   Magento_Object $row
     * @return  string
     */
    public function render(Magento_Object $row)
    {
        $name = $this->getColumn()->getName() ? $this->getColumn()->getName() : $this->getColumn()->getId();
        $html = '<select name="' . $this->escapeHtml($name) . '" ' . $this->getColumn()->getValidateClass() . '>';
        $value = $row->getData($this->getColumn()->getIndex());
        foreach ($this->_getOptions() as $val => $label) {
            $selected = ( ($val == $value && (!is_null($value))) ? ' selected="selected"' : '' );
            $html .= '<option value="' . $this->escapeHtml($val) . '"' . $selected . '>';
            $html .= $this->escapeHtml($label) . '</option>';
        }
        $html.='</select>';
        return $html;
    }

}
