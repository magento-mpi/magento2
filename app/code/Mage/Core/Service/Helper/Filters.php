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

    public function applyPaginationToCollection($collection, $request)
    {
        $limit = $request->getLimit();
        if ($limit) {
            $collection->setPageSize($limit);
        }

        $offset = $request->getOffset();
        if ($offset) {
            $collection->setCurPage($offset);
        }
    }

    public function applyFiltersToCollection($collection, $request)
    {
        $filters = $request->getFilters();
        if ($filters) {
            foreach ($filters as $key => $condition) {
                switch ($key) {
                    case '$and':
                        $this->applyAndConditionToCollection($collection, $condition);
                        break;
                    case '$or':
                        $this->applyOrConditionToCollection($collection, $condition);
                        break;
                    case '$func':
                        $this->applyFunctionalConditionToCollection($collection, $key, $condition);
                        break;
                    default:
                        $this->applyAttributeConditionToCollection($collection, $key, $condition);
                }
            }
        }
    }

    public function applyAndConditionToCollection($collection, $condition)
    {
        foreach ($condition as $attribute => $_condition) {
            $collection->addAttributeToFilter($attribute, $_condition);
        }
    }

    public function applyOrConditionToCollection($collection, $condition)
    {
        //
    }

    public function applyAttributeConditionToCollection($collection, $attribute, $condition)
    {
        $collection->addAttributeToFilter($attribute, $condition);
    }

    public function applyFunctionalConditionToCollection($collection, $method, $arguments)
    {
        $collection->$method($arguments);
    }
}
