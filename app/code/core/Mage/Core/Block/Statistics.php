<?php

class Mage_Core_Block_Statistics extends Mage_Core_Block_Abstract
{
    public function toHtml()
    {
        $timers = Varien_Profiler::getTimerSum();

        $out = '<div style="position:fixed;top:5px;left:5px; float : left;">';
        $out .= '<table border=1>';
        $out .= '<tr><th colspan="2">Debug Console</th></tr>';
        foreach ($timers as $name=>$sum) {
            $out .= '<tr><th>'.$name.'</th><td>'.number_format($sum,4).'</td></tr>';
        }
        $out .= '</table>';
        $out .= '<pre>';
        $out .= print_r(Varien_Profiler::getSqlProfiler(Mage::getSingleton('core/resource')->getConnection('dev_write')), 1);
        $out .= '</pre>';
        $out .= '</div>';
        return $out;
    }
}