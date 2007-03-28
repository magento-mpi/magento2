<?php

class Varien_Filter_Array_Grid extends Varien_Filter_Array
{
    function filter($grid)
    {
        $out = array();
        foreach ($grid as $i=>$array) {
            $out[$i] = parent::filter($array);
        }
        return $out;
    }
}