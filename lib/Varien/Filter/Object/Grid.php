<?php

class Varien_Filter_Object_Grid extends Varien_Filter_Object
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