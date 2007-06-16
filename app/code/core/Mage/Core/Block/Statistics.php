<?php

class Mage_Core_Block_Statistics extends Mage_Core_Block_Abstract 
{
    public function toHtml()
    {
        $timers = Varien_Profiler::getTimerSum();
        
        $out = '<hr><table border=1 align=center>';
        foreach ($timers as $name=>$sum) {
            $out .= '<tr><td>'.$name.'</td><td>'.number_format($sum,4).'</td></tr>';
        }
        $out .= '</table>';
        $out .= '<pre>';
        $out .= print_r(Varien_Profiler::getSqlProfiler(Mage::getSingleton('core/resource')->getConnection('dev_write')), 1);
        $out .= '</pre>';
        return $out;
    }
}