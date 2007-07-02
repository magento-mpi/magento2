<?php

class Mage_Core_Block_Profiler extends Mage_Core_Block_Abstract
{
    public function toHtml()
    {
        $timers = Varien_Profiler::getTimers();

        $out = '<div style1="position:fixed;bottom:5px;right:5px;opacity:.1;background:white" onmouseover1="this.style.opacity=1" onmouseout1="this.style.opacity=.1">';
        $out .= '<pre>Memory usage: real: '.memory_get_usage(true).', emalloc: '.memory_get_usage().'</pre>';
        $out .= '<table border=1 cellspacing=0 cellpadding=2 style="width:auto">';
        $out .= '<tr><th>Code Profiler</th><th>Time</th><th>Cnt</th></tr>';
        foreach ($timers as $name=>$timer) {
            $sum = Varien_Profiler::fetch($name,'sum');
            if ($sum<.0005) {
                continue;
            }
            $out .= '<tr><td>'.$name.'</td><td>'.number_format(Varien_Profiler::fetch($name,'sum'),4).'</td><td>'.Varien_Profiler::fetch($name,'count').'</td></tr>';
        }
        $out .= '</table>';
        $out .= '<pre>';
        $out .= print_r(Varien_Profiler::getSqlProfiler(Mage::getSingleton('core/resource')->getConnection('dev_write')), 1);
        $out .= '</pre>';
        $out .= '</div>';
        return $out;
    }
}