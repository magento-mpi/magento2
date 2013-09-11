<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Model\Widget\Grid;

class SubTotals extends \Magento\Backend\Model\Widget\Grid\TotalsAbstract
{
    /**
     * Count collection column sum based on column index
     *
     * @param string $index
     * @param \Magento\Data\Collection $collection
     * @return float|int
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
     * Count collection column average based on column index
     *
     * @param string $index
     * @param \Magento\Data\Collection $collection
     * @return float|int
     */
    protected function _countAverage($index, $collection)
    {
        $itemsCount = count($collection);
        return $itemsCount ? $this->_countSum($index, $collection) / $itemsCount : $itemsCount;
    }
}
