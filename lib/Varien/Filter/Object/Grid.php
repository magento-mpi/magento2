<?php
/**
 * {license_notice}
 *
 * @category   Varien
 * @package    Varien_Filter
 * @copyright  {copyright}
 * @license    {license_link}
 */


class Varien_Filter_Object_Grid extends Varien_Filter_Object
{
    function filter($grid)
    {
        $out = array();
        if (is_array($grid)) {
            foreach ($grid as $i=>$array) {
                $out[$i] = parent::filter($array);
            }
        }
        return $out;
    }
}