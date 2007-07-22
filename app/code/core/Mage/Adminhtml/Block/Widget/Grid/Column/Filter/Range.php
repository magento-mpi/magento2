<?php
/**
 * Range grid column filter
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 * @author      Michael Bessolov <michael@varien.com>
 */
class Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Range extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Abstract
{
    public function getHtml()
    {
        $html = '<div class="range"><div class="range-line"><span class="label">' . __('From').':</span> <input type="text" name="'.$this->_getHtmlName().'[from]" id="'.$this->_getHtmlId().'_from" value="'.$this->getEscapedValue('from').'" class="input-text"/></div>';
        $html .= '<div class="range-line"><span class="label">' . __('To').' : </span><input type="text" name="'.$this->_getHtmlName().'[to]" id="'.$this->_getHtmlId().'_to" value="'.$this->getEscapedValue('to').'" class="input-text"/></div></div>';
        return $html;
    }

    public function getValue($index=null)
    {
        if ($index) {
            return $this->getData('value', $index);
        }
        $value = $this->getData('value');
        if (!empty($value['from']) 
        	|| !empty($value['to'])
        	|| (isset($value['from']) && strlen($value['from']) > 0)
        	|| (isset($value['to']) && strlen($value['to']) > 0)) {
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