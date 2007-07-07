<?php
/**
 * Select grid column filter
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Abstract 
{
    protected function _getOptions()
    {
        return array();
    }
    public function getHtml()
    {
        $html = '<select name="">';
        foreach ($this->_getOptions() as $option){
            $html.= '<option value="'.$option['value'].'">'.$option['label'].'</option>';
        }
        $html.='</select>';
        return $html;
    }
}