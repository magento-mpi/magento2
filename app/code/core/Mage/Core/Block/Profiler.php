<?php

class Mage_Core_Block_Profiler extends Mage_Core_Block_Abstract
{
    public function toHtml()
    {
        $timers = Varien_Profiler::getTimers();

        $out = '<div style1="position:fixed;bottom:5px;right:5px;opacity:.1;background:white" onmouseover1="this.style.opacity=1" onmouseout1="this.style.opacity=.1">';
        $out .= '<table border=1 cellspacing=0 cellpadding=2 style="width:auto">';
        $out .= '<tr><th colspan="3">Debug Console</th></tr>';
        foreach ($timers as $name=>$timer) {
            $out .= '<tr><th>'.$name.'</th><td>'.number_format(Varien_Profiler::fetch($name,'sum'),4).'</td><td>'.Varien_Profiler::fetch($name,'count').'</td></tr>';
        }
        $out .= '</table>';
        $out .= '<pre>';
        $out .= print_r(Varien_Profiler::getSqlProfiler(Mage::getSingleton('core/resource')->getConnection('dev_write')), 1);
        $out .= '</pre>';
        $out .= '</div>';
        return $out;
    }
}