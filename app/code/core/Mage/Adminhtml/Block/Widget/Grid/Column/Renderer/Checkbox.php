<?php
/**
 * Grid checkbox column renderer
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Checkbox extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected $_values;
    
    public function getValues()
    {
        if (is_null($this->_values)) {
            $this->_values = $this->getColumn()->getData('values') ? $this->getColumn()->getData('values') : array();
        }
        return $this->_values;
    }
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $values = $this->getColumn()->getValues();
        $value  = $row->getData($this->getColumn()->getIndex());
        if (is_array($values)) {
            $checked = in_array($value, $values) ? ' checked="true"' : '';
        }
        else {
            $checked = ($value === $this->getColumn()->getValue()) ? ' checked="true"' : '';
        }
        return '<input type="checkbox" name="'.$this->getColumn()->getName().'" value="' . $row->getId() . '" class="checkbox"'.$checked.'/>';
    }
    
    public function renderHeader()
    {
        $checked = '';
        if ($filter = $this->getColumn()->getFilter()) {
            $checked = $filter->getValue() ? 'checked' : '';
        }
        return '<input type="checkbox" name="'.$this->getColumn()->getName().'" onclick="'.$this->getColumn()->getGrid()->getJsObjectName().'.checkCheckboxes(this)" class="checkbox" '.$checked.'/>';
    }
    
    public function renderProperty()
    {
        $out = 'width="55"';
        return $out;
    }

}
