<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Widget_Grid_SubTotals extends Mage_Backend_Model_Widget_Grid_Totals_Abstract
{
    /**
     * @param $index
     * @param $collection
     * @return mixed
     */
    protected function _countSum($index, $collection)
    {
        $sum = 0;
        foreach ($collection as $item) {
            $sum += $item[$index];
        }
        return $sum;
    }

    /**
     * @param $index
     * @param $collection
     * @return mixed
     */
    protected function _countAverage($index, $collection)
    {
        return $this->_countSum($index, $collection) / count($collection);
    }
}
