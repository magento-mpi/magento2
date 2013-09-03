<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Filter
 * @copyright  {copyright}
 * @license    {license_link}
 */


namespace Magento\Filter\Array;

class Grid extends \Magento\Filter\ArrayFilter
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
