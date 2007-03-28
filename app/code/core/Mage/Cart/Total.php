<?php

class Mage_Cart_Total
{
    static public function getTotals()
    {
        $totalsConfig = Mage::getConfig('Mage_Cart')->getTotals();
        
        $totals = array();

        foreach ($totalsConfig as $total) {
            $className = (string)$total->class;
            $obj = new $className();
            $t = $obj->getTotals();
            $totals = array_merge_recursive($totals, $t);
        }
        
        return $totals;
    }
}