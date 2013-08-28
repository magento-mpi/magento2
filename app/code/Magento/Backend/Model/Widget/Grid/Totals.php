<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Model_Widget_Grid_Totals extends Magento_Backend_Model_Widget_Grid_TotalsAbstract
{
    /**
     * Count collection column sum based on column index
     *
     * @param string $index
     * @param Magento_Data_Collection $collection
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
     * @param string $index
     * @param Magento_Data_Collection $collection
     * @return float|int
     */
    protected function _countAverage($index, $collection)
    {
        $itemsCount = 0;
        foreach ($collection as $item) {
            if (!$item->hasChildren()) {
                $itemsCount += 1;
            } else {
                $itemsCount += count($item->getChildren());
            }
        }

        return $itemsCount ? $this->_countSum($index, $collection) / $itemsCount : $itemsCount;
    }

}
