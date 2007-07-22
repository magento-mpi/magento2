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
        $value = $row->getData($this->getColumn()->getIndex());
        $checked = (in_array($value, $this->getValues())) ? ' checked="true"' : '';
        return '<input type="checkbox" name="'.$this->getColumn()->getName().'" value="' . $value . '" class="checkbox"'.$checked.'/>';
    }
    
    public function renderHeader()
    {
        return '<input type="checkbox" name="'.$this->getColumn()->getName().'" onclick="'.$this->getColumn()->getGrid()->getJsObjectName().'.checkCheckboxes(this)" class="checkbox"/>';
    }
    
    public function renderProperty()
    {
        $out = 'width="55"';
        return $out;
    }

}
