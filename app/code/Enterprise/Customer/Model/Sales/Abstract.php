<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer abstract model
 *
 */
abstract class Enterprise_Customer_Model_Sales_Abstract extends Magento_Core_Model_Abstract
{
    /**
     * Save new attribute
     *
     * @param Magento_Customer_Model_Attribute $attribute
     * @return Enterprise_Customer_Model_Sales_Abstract
     */
    public function saveNewAttribute(Magento_Customer_Model_Attribute $attribute)
    {
        $this->_getResource()->saveNewAttribute($attribute);
        return $this;
    }

    /**
     * Delete attribute
     *
     * @param Magento_Customer_Model_Attribute $attribute
     * @return Enterprise_Customer_Model_Sales_Abstract
     */
    public function deleteAttribute(Magento_Customer_Model_Attribute $attribute)
    {
        $this->_getResource()->deleteAttribute($attribute);
        return $this;
    }

    /**
     * Attach extended data to sales object
     *
     * @param Magento_Core_Model_Abstract $sales
     * @return Enterprise_Customer_Model_Sales_Abstract
     */
    public function attachAttributeData(Magento_Core_Model_Abstract $sales)
    {
        $sales->addData($this->getData());
        return $this;
    }

    /**
     * Save extended attributes data
     *
     * @param Magento_Core_Model_Abstract $sales
     * @return Enterprise_Customer_Model_Sales_Abstract
     */
    public function saveAttributeData(Magento_Core_Model_Abstract $sales)
    {
        $this->addData($sales->getData())
            ->setId($sales->getId())
            ->save();

        return $this;
    }

    /**
     * Processing object before save data.
     * Need to check if main entity is already deleted from the database:
     * we should not save additional attributes for deleted entities.
     *
     * @return Enterprise_Customer_Model_Sales_Abstract
     */
    protected function _beforeSave()
    {
        if ($this->_dataSaveAllowed && !$this->_getResource()->isEntityExists($this)) {
            $this->_dataSaveAllowed = false;
        }
        return parent::_beforeSave();
    }
}
