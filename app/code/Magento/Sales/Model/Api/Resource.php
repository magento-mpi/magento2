<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sale api resource abstract
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Api_Resource extends Magento_Api_Model_Resource_Abstract
{
    /**
     * Default ignored attribute codes per entity type
     *
     * @var array
     */
    protected $_ignoredAttributeCodes = array(
        'global'    =>  array('entity_id', 'attribute_set_id', 'entity_type_id')
    );

    /**
     * Attributes map array per entity type
     *
     * @var google
     */
    protected $_attributesMap = array(
        'global'    => array()
    );

    /** @var Magento_Api_Helper_Data */
    protected $_apiHelper;

    /**
     * Initialize dependencies.
     *
     * @param Magento_Api_Helper_Data $apiHelper
     */
    public function __construct(Magento_Api_Helper_Data $apiHelper)
    {
        $this->_apiHelper = $apiHelper;
    }

    /**
     * Update attributes for entity
     *
     * @param array $data
     * @param Magento_Core_Model_Abstract $object
     * @param array $attributes
     * @return Magento_Sales_Model_Api_Resource
     */
    protected function _updateAttributes($data, $object, $type,  array $attributes = null)
    {

        foreach ($data as $attribute=>$value) {
            if ($this->_apiHelper->isAttributeAllowed($attribute, $type, $this->_ignoredAttributeCodes, $attributes)) {
                $object->setData($attribute, $value);
            }
        }

        return $this;
    }

    /**
     * Retrieve entity attributes values
     *
     * @param Magento_Core_Model_Abstract $object
     * @param array $attributes
     * @return Magento_Sales_Model_Api_Resource
     */
    protected function _getAttributes($object, $type, array $attributes = null)
    {
        $result = array();

        if (!is_object($object)) {
            return $result;
        }

        foreach ($object->getData() as $attribute=>$value) {
            if ($this->_apiHelper->isAttributeAllowed($attribute, $type, $this->_ignoredAttributeCodes, $attributes)) {
                $result[$attribute] = $value;
            }
        }

        if (isset($this->_attributesMap['global'])) {
            foreach ($this->_attributesMap['global'] as $alias=>$attributeCode) {
                $result[$alias] = $object->getData($attributeCode);
            }
        }

        if (isset($this->_attributesMap[$type])) {
            foreach ($this->_attributesMap[$type] as $alias=>$attributeCode) {
                $result[$alias] = $object->getData($attributeCode);
            }
        }

        return $result;
    }
} // Class Magento_Sales_Model_Api_Resource End
