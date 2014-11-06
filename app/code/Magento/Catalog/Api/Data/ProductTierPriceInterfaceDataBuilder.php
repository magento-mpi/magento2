<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api\Data;

/**
 * DataBuilder class for \Magento\Catalog\Api\Data\ProductTierPriceInterface
 */
class ProductTierPriceInterfaceDataBuilder extends \Magento\Framework\Service\Data\ExtensibleDataBuilder
{
    /**
     * @param float $qty
     */
    public function setQty($qty)
    {
        $this->data['qty'] = $qty;
        return $this;
    }

    /**
     * @param float $value
     */
    public function setValue($value)
    {
        $this->data['value'] = $value;
        return $this;
    }

    public function populateWithArray(array $data)
    {
        $this->_data = array();
        $this->_setDataValues($data);
        return $this;
    }

    /**
     * Initializes Data Object with the data from array
     *
     * @param array $data
     * @return $this
     */
    protected function _setDataValues(array $data)
    {
        $dataObjectMethods = get_class_methods($this->_getDataObjectType());
        foreach ($data as $key => $value) {
            /* First, verify is there any getter for the key on the Service Data Object */
            $possibleMethods = array(
                'get' . \Magento\Framework\Service\SimpleDataObjectConverter::snakeCaseToUpperCamelCase($key),
                'is' . \Magento\Framework\Service\SimpleDataObjectConverter::snakeCaseToUpperCamelCase($key)
            );
            if (array_intersect($possibleMethods, $dataObjectMethods)) {
                $this->data[$key] = $value;
            }
        }
        return $this;
    }

    /**
     * Return the Data type class name
     *
     * @return string
     */
    protected function _getDataObjectType()
    {
        return substr(get_class($this), 0, -11);
    }


    /**
     * Initialize the builder
     *
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        parent::__construct($objectManager, 'Magento\Catalog\Api\Data\ProductTierPriceInterface');
    }
}
