<?php
/**
 * Grid column widget for rendering grid cells that contains boolean values
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */
class Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Boolean extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
    public function render(Varien_Object $row)
    {
		$value = intval($row->getData($this->getColumn()->getIndex()));
		$this->_toHtml($value);
    }

    protected function _toHtml($value)
    {
        $values = $this->getColumn()->getValues();
        if( is_array($values) && count($values) > 0 ) {
            echo $values[$value];
        } else {
            echo ( $value ) ? __('Yes') : __('No');
        }
    }
}