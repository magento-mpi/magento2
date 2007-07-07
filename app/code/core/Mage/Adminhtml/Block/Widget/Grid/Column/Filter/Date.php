<?php
/**
 * Date grid column filter
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Date extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Abstract 
{
    public function getHtml()
    {
        $html = __('From').': <input type="text" name="'.$this->_getHtmlName().'[from]" id="'.$this->_getHtmlId().'_from" value="'.$this->getEscapedValue('from').'"/><br/>';
        $html.= __('To').' :<input type="text" name="'.$this->_getHtmlName().'[to]" id="'.$this->_getHtmlId().'_to" value="'.$this->getEscapedValue('to').'"/>';
        $html.= '<script type="text/javascript">
            Calendar.setup({
                inputField     :    "'.$this->_getHtmlId().'_from",   
                ifFormat       :    "%Y-%m-%d",
                showsTime      :    false
            });
            Calendar.setup({
                inputField     :    "'.$this->_getHtmlId().'_to",   
                ifFormat       :    "%Y-%m-%d",
                showsTime      :    false
            });
        </script>';
        return $html;
    }
    
    public function getValue($index=null)
    {
        if ($index) {
            return $this->getData('value', $index);
        }
        $value = $this->getData('value');
        if (!empty($value['from']) || !empty($value['to'])) {
            return $value;
        }
        return null;
    }
    
    public function getCondition()
    {
        $value = $this->getValue();
        return $value;
    }    
}