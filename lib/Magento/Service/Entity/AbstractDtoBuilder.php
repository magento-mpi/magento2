<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Service\Entity;

use Magento\Service\Entity\AbstractDto;

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
     * @return AbstractDtoBuilder
     */
    public function populate(AbstractDto $prototype)
    {
        $this->_data = array();
        foreach (get_class_methods(get_class($prototype)) as $method) {
            if (substr($method, 0, 3) === 'get') {
                $originalDataName = lcfirst(substr($method, 3));
                $dataName = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $originalDataName));

                if ($dataName === 'attribute' || $dataName === 'attributes') {
                    continue;
                } else {
                    $value = $prototype->$method();
                    if ($value !== null) {
                        $this->_data[$dataName] = $prototype->$method();
                    }
                }
            } elseif (substr($method, 0, 2) == 'is') {
                $originalDataName = lcfirst(substr($method, 2));
                $dataName = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $originalDataName));

                $this->_data[$dataName] = $prototype->$method();
            }
        }

        return $this;
    }

    /**
     * Populates the fields with data from the array.
     *
     * @param array $data
     * @return self
     */
    public function populateWithArray(array $data)
    {
        $this->_data = $data;
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
        $dtoType = substr(get_class($this), 0, -7);
        $retObj = new $dtoType($this->_data);
        $this->_data = array();
        return $retObj;
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return self
     */
    protected function _set($key, $value)
    {
        $this->_data[$key] = $value;
        return $this;
    }
}
