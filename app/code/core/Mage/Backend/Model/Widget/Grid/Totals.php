<?php

class Mage_Backend_Model_Widget_Grid_Totals extends Mage_Backend_Model_Widget_Grid_Totals_Abstract
{
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

        return $this->_countSum($index, $collection) / $numRows;
    }

}
