<?php
/**
 * Service Helper.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Service_Helper_Filters extends Mage_Core_Service_Helper_Abstract
{
    public function applyFiltersToCollection($collection, array $filters = array())
    {
        foreach ($filters as $key => $value) {
            $method = '_' . $key;
            $this->$method($collection, $value);
        }
    }

    protected function _limit($collection, $value)
    {
        $collection->setPageSize($value);
    }

    protected function _offset($collection, $value)
    {
        $collection->setCurPage($value);
    }
}
