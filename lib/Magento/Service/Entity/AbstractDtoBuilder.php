<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Service\Entity;

abstract class AbstractDtoBuilder
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
     * @param AbstractDto $prototype the prototype to base on
     * @return $this
     * @throws \LogicException If $prototype object class is not the same type as object that is constructed
     */
    public function populate(AbstractDto $prototype)
    {
        $dtoType = substr(get_class($this), 0, -7);
        if (get_class($prototype) != $dtoType) {
            throw new \LogicException('Wrong prototype object');
        }
        return $this->populateWithArray($prototype->__toArray());
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
        $dtoMethods = get_class_methods(get_class($this));
        foreach ($data as $key => $value) {
            $method = 'set' . $this->_snakeCaseToCamelCase($key);
            if (in_array($method, $dtoMethods)) {
                $this->$method($value);
            } else {
                $this->_data[$key] = $value;
            }
        }

        return $this;
    }

    /**
     * Merge second DTO data with first DTO data and create new DTO object based on merge result.
     *
     * @param AbstractDto $firstDto
     * @param AbstractDto $secondDto
     * @return AbstractDto
     */
    public function mergeDtos(AbstractDto $firstDto, AbstractDto $secondDto)
    {
        $this->_data = array_merge($firstDto->__toArray(), $secondDto->__toArray());
        return $this->create();
    }

    /**
     * Merged data provided in array format with DTO data and create new DTO object based on merge result.
     *
     * @param AbstractDto $dto
     * @param array $data
     * @return AbstractDto
     */
    public function mergeDtoWithArray(AbstractDto $dto, array $data)
    {
        $this->_data = array_merge($dto->__toArray(), $data);
        return $this->create();
    }

    /**
     * Builds the entity.
     *
     * @return AbstractDto
     */
    public function create()
    {
        $dtoType = $this->_getDtoType();
        $retObj = new $dtoType($this);
        $this->_data = array();
        return $retObj;
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
     * Return the Dto type class name
     *
     * @return string
     */
    protected function _getDtoType()
    {
        return substr(get_class($this), 0, -7);
    }

    /**
     * Converts an input string from snake_case to upper CamelCase.
     *
     * @param string $input
     * @return string
     */
    protected function _snakeCaseToCamelCase($input)
    {
        $output = '';
        $segments = explode('_', $input);
        foreach ($segments as $segment) {
            $output .= ucfirst($segment);
        }
        return $output;
    }

    /**
     * Return DTO data.
     *
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }
}
