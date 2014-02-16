<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Filter
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Filter\GridArray;

class Grid extends \Magento\Filter\ArrayFilter
{
    /**
     * @param array $grid
     * @return array
     */
    public function filter($grid)
    {
        $out = array();
        foreach ($grid as $i => $array) {
            $out[$i] = parent::filter($array);
        }
        return $out;
    }
}
