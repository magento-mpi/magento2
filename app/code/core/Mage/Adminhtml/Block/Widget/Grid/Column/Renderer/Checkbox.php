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
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        return '<input type="checkbox" name="" value="' . $row->getId() . '" class="checkbox"/>';
    }
    
    public function renderHeader()
    {
        return '<input type="checkbox" name="" value="" class="checkbox"/>';
    }
    
    public function renderProperty()
    {
        $out = 'width="55"';
        return $out;
    }

}
