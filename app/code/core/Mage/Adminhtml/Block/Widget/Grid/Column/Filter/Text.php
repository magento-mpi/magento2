<?php
/**
 * Text grid column filter
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Text extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Abstract 
{
    public function getHtml()
    {
        $html = '<input type="text" name="'.$this->_getHtmlName().'" id="'.$this->_getHtmlId().'" value="'.$this->getEscapedValue().'"/>';
        return $html;
    }
}
