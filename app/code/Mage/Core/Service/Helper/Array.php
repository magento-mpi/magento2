<?php
/**
 * Service Array Helper.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Service_Helper_Array
{
    /**
     * Prepare collection for response
     *
     * @param Varien_Data_Collection $collection
     * @param array $request
     * @return array
     */
    public function collectionToArray($collection, $request)
    {
        foreach ($collection->getItems() as $item) {
            $array[$item->getId()] = $this->modelToArray($item, $request);
        }

        return $array;
    }

    /**
     * Convert model to array
     *
     * @param Mage_Core_Model_Abstract $model
     * @param mixed $request
     * @return bool
     */
    public function modelToArray($model, $request)
    {
        $fields = !empty($request['fields']) ? $request['fields'] : array();

        $array = $model->toArray($fields);

        return $array;
    }
}
