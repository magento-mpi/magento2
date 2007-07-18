<?php
/**
 * Date grid column filter
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 * @todo        date format
 */
class Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Date extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Abstract
{
    protected function _afterSetLayout()
    {
        if ($this->getLayout()->getBlock('root')) {
            $this->getLayout()->getBlock('root')->setCanLoadCalendarJs(true);
        }        
        return $this;
    }

    public function getHtml()
    {      
        if (!($format = Mage::getStoreConfig('general/local/date_format_short'))) {
            $format = '%m/%e/%Y';
        }
        
        $html = '<div class="range"><div class="date"><span class="label">' . __('From').':</span> <input type="text" name="'.$this->_getHtmlName().'[from]" id="'.$this->_getHtmlId().'_from" value="'.$this->getEscapedValue('from').'" class="input-text"/> <img src="' . Mage::getBaseUrl() . '/skins/adminhtml/images/grid-cal.gif" alt="" align="absmiddle" id="'.$this->_getHtmlId().'_from_trig" title="Date selector" /></div>';
        $html.= '<div class="date"><span class="label">' . __('To').' :</span> <input type="text" name="'.$this->_getHtmlName().'[to]" id="'.$this->_getHtmlId().'_to" value="'.$this->getEscapedValue('to').'" class="input-text"/> <img src="' . Mage::getBaseUrl() . '/skins/adminhtml/images/grid-cal.gif" alt="" align="absmiddle" id="'.$this->_getHtmlId().'_to_trig" title="Date selector" /></div></div>';
        $html.= '<script type="text/javascript">
            Calendar.setup({
                inputField : "'.$this->_getHtmlId().'_from",
                ifFormat : "'.$format.'",
                button : "'.$this->_getHtmlId().'_from_trig",
                align : "Bl",
                singleClick : true
            });
            Calendar.setup({
                inputField : "'.$this->_getHtmlId().'_to",
                ifFormat : "'.$format.'",
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
            if ($data = $this->getData('value', $index)) {
                return $data;//date('Y-m-d', strtotime($data));
            }
            return null;
        }
        $value = $this->getData('value');
        if (is_array($value)) {
            $value['date'] = true;
        }
        return $value;
    }

    public function getCondition()
    {
        $value = $this->getValue();
        return $value;
    }
}