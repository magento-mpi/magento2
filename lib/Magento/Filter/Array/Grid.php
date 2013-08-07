<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Filter
 * @copyright  {copyright}
 * @license    {license_link}
 */


class Magento_Filter_Array_Grid extends Magento_Filter_Array
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