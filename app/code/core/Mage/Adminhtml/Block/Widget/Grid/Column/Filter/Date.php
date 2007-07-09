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
        $html = '<div class="range date"><span class="label">' . __('From').':</span> <input type="text" name="'.$this->_getHtmlName().'[from]" id="'.$this->_getHtmlId().'_from" value="'.$this->getEscapedValue('from').'" class="input-text"/> <img src="' . Mage::getBaseUrl() . '/skins/adminhtml/images/grid-cal.gif" alt="" align="absmiddle" id="'.$this->_getHtmlId().'_from_trig" title="Date selector" /></div>';
        $html.= '<div class="range date"><span class="label">' . __('To').' :</span> <input type="text" name="'.$this->_getHtmlName().'[to]" id="'.$this->_getHtmlId().'_to" value="'.$this->getEscapedValue('to').'" class="input-text"/> <img src="' . Mage::getBaseUrl() . '/skins/adminhtml/images/grid-cal.gif" alt="" align="absmiddle" id="'.$this->_getHtmlId().'_to_trig" title="Date selector" /></div>';
        $html.= '<script type="text/javascript">
            Calendar.setup({
                inputField : "'.$this->_getHtmlId().'_from",
                ifFormat : "%m/%e/%Y",
                button : "'.$this->_getHtmlId().'_from_trig",
                align : "Bl",
                singleClick : true
            });
            Calendar.setup({
                inputField : "'.$this->_getHtmlId().'_to",
                ifFormat : "%m/%e/%Y",
                button : "'.$this->_getHtmlId().'_to_trig",
                align : "Bl",
                singleClick : true
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