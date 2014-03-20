<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerCustomAttributes\Model\Sales;

/**
 * Customer abstract model
 *
 */
abstract class AbstractSales extends \Magento\Model\AbstractModel
{
    /**
     * Save new attribute
     *
     * @param \Magento\Customer\Model\Attribute $attribute
     * @return $this
     */
    public function saveNewAttribute(\Magento\Customer\Model\Attribute $attribute)
    {
        $this->_getResource()->saveNewAttribute($attribute);
        return $this;
    }

    /**
     * Delete attribute
     *
     * @param \Magento\Customer\Model\Attribute $attribute
     * @return $this
     */
    public function deleteAttribute(\Magento\Customer\Model\Attribute $attribute)
    {
        $this->_getResource()->deleteAttribute($attribute);
        return $this;
    }

    /**
     * Attach extended data to sales object
     *
     * @param \Magento\Model\AbstractModel $sales
     * @return $this
     */
    public function attachAttributeData(\Magento\Model\AbstractModel $sales)
    {
        $sales->addData($this->getData());
        return $this;
    }

    /**
     * Save extended attributes data
     *
     * @param \Magento\Model\AbstractModel $sales
     * @return $this
     */
    public function saveAttributeData(\Magento\Model\AbstractModel $sales)
    {
        $this->addData($sales->getData())->setId($sales->getId())->save();

        return $this;
    }

    /**
     * Processing object before save data.
     * Need to check if main entity is already deleted from the database:
     * we should not save additional attributes for deleted entities.
     *
     * @return $this
     */
    protected function _beforeSave()
    {
        if ($this->_dataSaveAllowed && !$this->_getResource()->isEntityExists($this)) {
            $this->_dataSaveAllowed = false;
        }
        return parent::_beforeSave();
    }
}
