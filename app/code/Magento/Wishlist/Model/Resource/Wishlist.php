<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Wishlist resource model
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Wishlist_Model_Resource_Wishlist extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Store wishlist items count
     *
     * @var null|int
     */
    protected $_itemsCount = null;

    /**
     * Store customer ID field name
     *
     * @var string
     */
    protected $_customerIdFieldName = 'customer_id';

    /**
     * Set main entity table name and primary key field name
     */
    protected function _construct()
    {
        $this->_init('wishlist', 'wishlist_id');
    }

    /**
     * Prepare wishlist load select query
     *
     * @param string $field
     * @param mixed $value
     * @param mixed $object
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        if ($field == $this->_customerIdFieldName) {
            $select->order('wishlist_id ' . Zend_Db_Select::SQL_ASC)
                ->limit(1);
        }
        return $select;
    }

    /**
     * Getter for customer ID field name
     *
     * @return string
     */
    public function getCustomerIdFieldName()
    {
        return $this->_customerIdFieldName;
    }

    /**
     * Setter for customer ID field name
     *
     * @param $fieldName
     *
     * @return Magento_Wishlist_Model_Resource_Wishlist
     */
    public function setCustomerIdFieldName($fieldName)
    {
        $this->_customerIdFieldName = $fieldName;
        return $this;
    }
}
