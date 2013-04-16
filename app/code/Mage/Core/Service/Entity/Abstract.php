<?php
/**
 * Abstract entity API service.
 *
 * Purpose: expose and manage business entities.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
abstract class Mage_Core_Service_Entity_Abstract
{
    /**
     * Return model which operated by current service.
     *
     * @param mixed  $objectId
     * @param string $fieldsetId
     * @return Varien_Object
     */
    abstract protected function _getObject($objectId, $fieldsetId = '');

    /**
     * Get collection of objects of the current service.
     *
     * @param array  $objectIds
     * @param string $fieldsetId
     * @return Varien_Data_Collection_Db
     */
    abstract protected function _getObjectCollection(array $objectIds = array(), $fieldsetId = '');

    /**
     * Return schema with data.
     *
     * @param array         $data   Already fetched data from object
     * @param Varien_Object $object
     * @return array
     */
    abstract protected function _applySchema(array $data, Varien_Object $object);

    /**
     * Extract data out of the project object retrieved by ID.
     *
     * @param mixed  $objectId
     * @param string $fieldsetId
     * @return array
     */
    protected function _getData($objectId, $fieldsetId = '')
    {
        $data = array();
        $object = $this->_getObject($objectId, $fieldsetId);

        if ($object->getId()) {
            $data = $this->_getObjectData($object);
            $data = $this->_applySchema($data, $object);
        }

        return $data;
    }

    /**
     * Extract data from the loaded object and make it conform with schema.
     *
     * @param Varien_Object $object
     * @return array
     */
    protected function _getObjectData(Varien_Object $object)
    {
        $data = $object->getData();
        $underscoreToCamelCase = new Zend_Filter_Word_UnderscoreToCamelCase();

        // Make camelCase out of underscore
        foreach ($data as $key => $value) {
            $camelCase = $underscoreToCamelCase->filter($key);

            if ($camelCase !== $key) {
                $data[$camelCase] = $data[$key];
                unset($data[$key]);
            }
        }

        $data = $this->_formatObjectData($data);

        return $data;
    }

    /**
     * Get data from several objects at once.
     *
     * @param array  $objectIds
     * @param string $fieldsetId
     * @return array
     */
    protected function _getCollectionData(array $objectIds = array(), $fieldsetId = '')
    {
        $collection = $this->_getObjectCollection($objectIds, $fieldsetId);
        $dataCollection = array();

        foreach ($collection as $item) {
            /** @var $item Mage_Core_Model_Abstract */
            $item->load($item->getId());
            $dataCollectionItem = $this->_getObjectData($item);
            $dataCollectionItem = $this->_applySchema($dataCollectionItem, $item);
            $dataCollection[] = $dataCollectionItem;
        }

        return $dataCollection;
    }

    /**
     * Format object's data so it represents an array on all levels.
     * @todo Decide what to do with objects
     *
     * @param array $data
     * @return array
     */
    protected function _formatObjectData(array $data)
    {
        foreach ($data as $key => $value) {
            if (is_object($value)) {
                unset($data[$key]);
            } else if (is_array($value)) {
                $data[$key] = $this->_formatObjectData($value);
            }
        }

        return $data;
    }

    /**
     * Return fields given fieldset ID.
     *
     * @param string $fieldsetId
     * @return array
     */
    protected function _getFieldset($fieldsetId)
    {
        // @todo
//        $fields = Mage::getConfig()->getNode('global/fieldset/' . $fieldsetId)->asArray();
//
//        return array_keys($fields);
    }
}
