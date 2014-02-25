<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Service\Entity;

/**
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class AbstractObjectBuilder
{
    /**
     * @var array
     */
    protected $_data;

    /**
     * Initialize internal storage
     */
    public function __construct()
    {
        $this->_data = array();
    }

    /**
     * Populates the fields with an existing entity.
     *
     * @param AbstractObject $prototype the prototype to base on
     * @return $this
     * @throws \LogicException If $prototype object class is not the same type as object that is constructed
     */
    public function populate(AbstractObject $prototype)
    {
        $objectType = $this->_getDataObjectType();
        if (get_class($prototype) != $objectType) {
            throw new \LogicException('Wrong prototype object given. It can only be of "' . $objectType . '" type.');
        }
        return $this->populateWithArray($prototype->__toArray());
    }

    /**
     * Template method used to configure the attribute codes for the custom attributes
     *
     * @return array
     */
    public function getCustomAttributesCodes()
    {
        return [];
    }

    /**
     * Populates the fields with data from the array.
     *
     * Keys for the map are snake_case attribute/field names.
     *
     * @param array $data
     * @return $this
     */
    public function populateWithArray(array $data)
    {
        $this->_data = [];
        $dataObjectMethods = get_class_methods($this->_getDataObjectType());
        $customAttributesCodes = $this->getCustomAttributesCodes();
        foreach ($data as $key => $value) {
            /* First, verify is there any getter for the key on the Service Data Object */
            $possibleMethods = ['get' . $this->_snakeCaseToCamelCase($key), 'is' . $this->_snakeCaseToCamelCase($key)];
            if (array_intersect($possibleMethods, $dataObjectMethods)) {
                $this->_data[$key] = $value;
            } elseif (in_array($key, $customAttributesCodes)) {
                /* If key corresponds to custom attribute code, populate custom attributes */
                $this->_data[AbstractObject::CUSTOM_ATTRIBUTES_KEY][$key] = $value;
            }
        }
        return $this;
    }

    /**
     * Merge second Data Object data with first Data Object data and create new Data Object object based on merge
     * result.
     *
     * @param AbstractObject $firstDataObject
     * @param AbstractObject $secondDataObject
     * @return AbstractObject
     */
    public function mergeDataObjects(AbstractObject $firstDataObject, AbstractObject $secondDataObject)
    {
        $this->_data = array_merge($firstDataObject->__toArray(), $secondDataObject->__toArray());
        return $this->create();
    }

    /**
     * Merged data provided in array format with Data Object data and create new Data Object object based on merge
     * result.
     *
     * @param AbstractObject $dataObject
     * @param array $data
     * @return AbstractObject
     */
    public function mergeDataObjectWithArray(AbstractObject $dataObject, array $data)
    {
        $this->_data = array_merge($dataObject->__toArray(), $data);
        return $this->create();
    }

    /**
     * Builds the entity.
     *
     * @return AbstractObject
     */
    public function create()
    {
        $dataObjectType = $this->_getDataObjectType();
        $dataObject = new $dataObjectType($this);
        $this->_data = array();
        return $dataObject;
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return $this
     */
    protected function _set($key, $value)
    {
        $this->_data[$key] = $value;
        return $this;
    }

    /**
     * Return the Data type class name
     *
     * @return string
     */
    private function _getDataObjectType()
    {
        return substr(get_class($this), 0, -7);
    }

    /**
     * Converts an input string from snake_case to upper CamelCase.
     *
     * @param string $input
     * @return string
     */
    private function _snakeCaseToCamelCase($input)
    {
        $output = '';
        $segments = explode('_', $input);
        foreach ($segments as $segment) {
            $output .= ucfirst($segment);
        }
        return $output;
    }

    /**
     * Return data Object data.
     *
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }
}
