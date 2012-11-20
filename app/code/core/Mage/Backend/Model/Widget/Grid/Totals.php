<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Widget_Grid_Totals extends Mage_Backend_Model_Widget_Grid_Totals_Abstract
{
    /**
     * Count collection column sum based on column index
     *
     * @param $index
     * @param $collection
     * @return float|int
     */
    protected function _countSum($index, $collection)
    {
        $sum = 0;
        foreach ($collection as $item) {
            if (!$item->hasChildren()) {
                $sum += $item[$index];
            } else {
                $sum += $this->_countSum($index, $item->getChildren());
            }

        }
        return $sum;
    }

    /**
     * Count collection column average based on column index
     *
     * @param $index
     * @param $collection
     * @return float|int
     */
    protected function _countAverage($index, $collection)
    {
        $numRows = 0;
        foreach ($collection as $item) {
            if (!$item->hasChildren()) {
                $numRows += 1;
            } else {
                $numRows += count($item->getChildren());
            }
        }

        return ($numRows)? $this->_countSum($index, $collection) / $numRows : $numRows;
    }

}
