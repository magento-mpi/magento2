<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Filter
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\Filter\Object;

use Magento\Object;

class Grid extends \Magento\Filter\Object
{
    /**
     * @param Object[] $grid
     * @return Object[]
     */
    public function filter($grid)
    {
        $out = array();
        if (is_array($grid)) {
            foreach ($grid as $i => $array) {
                $out[$i] = parent::filter($array);
            }
        }
        return $out;
    }
}
