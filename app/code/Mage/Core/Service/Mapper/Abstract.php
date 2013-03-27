<?php
/**
 * Service schema abstract mapper.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
abstract class Mage_Core_Service_Mapper_Abstract
{
    /**
     * Retrieves full unfiltered schema for the object.
     *
     * @return array
     */
    abstract protected function _getSchema();

    /**
     * Retrieve value from object for the given schema element.
     *
     * @param string        $schemaElement Element of the schema
     * @param Varien_Object $object        Object from which values are going to be retrieved from
     * @return mixed
     */
    abstract protected function _map($schemaElement, Varien_Object $object);

    /**
     * Map object values to schema.
     *
     * @param array         $data          Data that has been already retrieved from the object
     * @param Varien_Object $object        Object where values are going to be taken from
     * @param array         $includeFields Return only data for these schema fields
     * @param array         $excludeFields Return data for all schema, except these fields
     * @return array
     */
    public function apply(array $data, Varien_Object $object, $includeFields = array(), $excludeFields = array())
    {
        $schemaWithData = $data;
        $schema = $this->_prepareSchema($includeFields, $excludeFields);

        foreach ($schema as $element) {
            $schemaWithData[$element] = $this->_map($element, $object);
        }

        return $schemaWithData;
    }

    /**
     * Returns only that part of schema which is requested.
     *
     * @param array $includeFields Return only these fields from schema
     * @param array $excludeFields Return all schema, except these fields
     * @return array
     * @throws Mage_Core_Service_Mapper_Exception
     */
    protected function _prepareSchema($includeFields = array(), $excludeFields = array())
    {
        if (!empty($includeFields) && !empty($excludeFields)) {
            throw new Mage_Core_Service_Mapper_Exception(
                'Input parameters inconsistency: you should specify either include or exclude fields, not both'
            );
        }

        if (!empty($includeFields)) {
            return $includeFields;
        }

        $schema = $this->_getSchema();

        foreach ($excludeFields as $field) {
            if (isset($schema[$field])) {
                unset($schema[$field]);
            }
        }

        return $schema;
    }
}
