<?php
/**
 * Grid input column renderer
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected $_values;

    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        return '<input type="text" name="'.( $this->getColumn()->getName() ? $this->getColumn()->getName() : $this->getColumn()->getId() ).'" value="'
               . $row->getData($this->getColumn()->getIndex()) . '" class="input-text"/>';
    }
}
