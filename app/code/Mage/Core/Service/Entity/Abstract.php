<?php
/**
 * Abstract entity API service.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
abstract class Mage_Core_Service_Entity_Abstract extends Mage_Core_Service_Abstract
{
    /**
     * Array key which represents related data
     */
    const RELATED_DATA_KEY = '_related_data';

    /** @var Magento_ObjectManager */
    protected $_objectManager;

    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Returns model which operated by current service.
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
    abstract protected function _getObjectCollection(array $objectIds, $fieldsetId = '');

    /**
     * Extract data out of the project object retrieved by ID.
     *
     * @param mixed  $objectId
     * @param string $methodId
     * @param string $fieldsetId
     * @return array
     */
    protected function _getData($objectId, $methodId, $fieldsetId = '')
    {
        $data = array();
        $object = $this->_getObject($objectId, $fieldsetId);

        if ($object->getId()) {
            $data = $this->_getObjectData($object);
            $data = $this->_applySchema($data, $object, $methodId);
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

        // Make camelCase out of underscore
        foreach ($data as $key => $value) {
            $camelCase = preg_replace_callback(
                '/_(.)/',
                function ($matches) { return strtoupper($matches[1]);},
                $key
            );

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
     * @param string $methodId
     * @param string $fieldsetId
     * @return array
     */
    protected function _getCollectionData(array $objectIds, $methodId, $fieldsetId = '')
    {
        $collection = $this->_getObjectCollection($objectIds, $fieldsetId);
        $dataCollection = array();

        foreach ($collection as $item) {
            /** @var $item Varien_Object */
            $dataCollection[] = $this->_getObjectData($item);
            $dataCollection = $this->_applySchema($dataCollection, $item, $methodId);
        }

        return $dataCollection;
    }

    /**
     * Adds additional data to the model data. Outside service may require some additional parameters, e.g. URL for
     * product. We can't just merge that into model data as user might have defined an attribute with same name, so
     * there already is going to be array key with same name. We're setting it to special section, name of which can
     * not be used as attribute name.
     *
     * @param array $mainData    Original model data (for product: price, sku, description, etc.)
     * @param array $relatedData Additional data to be added to the $mainData, such as product URL
     * @return array
     */
    protected function _setRelatedData(array $mainData, array $relatedData)
    {
        $mainData += array(
            static::RELATED_DATA_KEY => $relatedData
        );

        return $mainData;
    }

    /**
     * Makes passed data to be compatible with schema.
     *
     * @param array         $data
     * @param Varien_Object $object
     * @param string        $methodId
     * @return array
     */
    protected function _applySchema(array $data, Varien_Object $object, $methodId)
    {
        // @todo
    }

    /**
     * Formats object's data so it represents an array on all levels.
     * @todo Decide what to do with objects
     *
     * @param array $data
     * @return array
     */
    protected function _formatObjectData(array $data)
    {
        foreach ($data as $key => $value) {
            if (is_object($value)) {
                $data[$key] = '**OBJECT**';
            } else if (is_array($value)) {
                $data[$key] = $this->_formatObjectData($value);
            }
        }

        return $data;
    }

    /**
     * Returns fields given fieldset ID.
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
