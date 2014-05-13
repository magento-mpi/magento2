<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\Framework\Filter\GridArray;

class Grid extends \Magento\Framework\Filter\ArrayFilter
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
