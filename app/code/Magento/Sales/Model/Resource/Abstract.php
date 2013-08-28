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
 * Sales abstract resource model
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_Sales_Model_Resource_Abstract extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Data converter object
     *
     * @var Magento_Sales_Model_ConverterInterface
     */
    protected $_converter = null;

    /**
     * Prepare data for save
     *
     * @param Magento_Core_Model_Abstract $object
     * @return array
     */
    protected function _prepareDataForSave(Magento_Core_Model_Abstract $object)
    {
        $currentTime = Magento_Date::now();
        if ((!$object->getId() || $object->isObjectNew()) && !$object->getCreatedAt()) {
            $object->setCreatedAt($currentTime);
        }
        $object->setUpdatedAt($currentTime);
        $data = parent::_prepareDataForSave($object);
        return $data;
    }

    /**
     * Check if current model data should be converted
     *
     * @return bool
     */
    protected function _shouldBeConverted()
    {
        return (null !== $this->_converter);
    }


    /**
     * Perform actions before object save
     *
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_Sales_Model_Resource_Abstract
     */
    protected function _beforeSave(Magento_Core_Model_Abstract $object)
    {
        parent::_beforeSave($object);

        if (true == $this->_shouldBeConverted()) {
            foreach($object->getData() as $fieldName => $fieldValue) {
                $object->setData($fieldName, $this->_converter->encode($object, $fieldName));
            }
        }
        return $this;
    }

    /**
     * Perform actions after object save
     *
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_Sales_Model_Resource_Abstract
     */
    protected function _afterSave(Magento_Core_Model_Abstract $object)
    {
        if (true == $this->_shouldBeConverted()) {
            foreach($object->getData() as $fieldName => $fieldValue) {
                $object->setData($fieldName, $this->_converter->decode($object, $fieldName));
            }
        }
        return parent::_afterSave($object);
    }

    /**
     * Perform actions after object load
     *
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_Sales_Model_Resource_Abstract
     */
    protected function _afterLoad(Magento_Core_Model_Abstract $object)
    {
        if (true == $this->_shouldBeConverted()) {
            foreach($object->getData() as $fieldName => $fieldValue) {
                $object->setData($fieldName, $this->_converter->decode($object, $fieldName));
            }
        }
        return parent::_afterLoad($object);
    }
}
