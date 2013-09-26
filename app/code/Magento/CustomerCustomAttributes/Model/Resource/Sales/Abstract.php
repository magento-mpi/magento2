<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customer Sales abstract resource
 */
abstract class Magento_CustomerCustomAttributes_Model_Resource_Sales_Abstract extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Used us prefix to name of column table
     *
     * @var null | string
     */
    protected $_columnPrefix       = 'customer';

    /**
     * Primery key auto increment flag
     *
     * @var bool
     */
    protected $_isPkAutoIncrement  = false;

    /**
     * Return column name for attribute
     *
     * @param Magento_Customer_Model_Attribute $attribute
     * @return string
     */
    protected function _getColumnName(Magento_Customer_Model_Attribute $attribute)
    {
        $columnName = $attribute->getAttributeCode();
        if ($this->_columnPrefix) {
            $columnName = sprintf('%s_%s', $this->_columnPrefix, $columnName);
        }
        return $columnName;
    }

    /**
     * Saves a new attribute
     *
     * @param Magento_Customer_Model_Attribute $attribute
     * @return Magento_CustomerCustomAttributes_Model_Resource_Sales_Abstract
     */
    public function saveNewAttribute(Magento_Customer_Model_Attribute $attribute)
    {
        $backendType = $attribute->getBackendType();
        if ($backendType == Magento_Customer_Model_Attribute::TYPE_STATIC) {
            return $this;
        }

        switch ($backendType) {
            case 'datetime':
                $definition = array(
                    'type'      => Magento_DB_Ddl_Table::TYPE_DATE,
                );
                break;
            case 'decimal':
                $definition = array(
                    'type'      => Magento_DB_Ddl_Table::TYPE_DECIMAL,
                    'length'    => 12,4,
                );
                break;
            case 'int':
                $definition = array(
                    'type'      => Magento_DB_Ddl_Table::TYPE_INTEGER,
                );
                break;
            case 'text':
                $definition = array(
                    'type'      => Magento_DB_Ddl_Table::TYPE_TEXT,
                );
                break;
            case 'varchar':
                $definition = array(
                    'type'      => Magento_DB_Ddl_Table::TYPE_TEXT,
                    'length'    => 255,
                );
                break;
            default:
                return $this;
        }

        $columnName = $this->_getColumnName($attribute);
        $definition['comment'] = ucwords(str_replace('_', ' ', $columnName));
        $this->_getWriteAdapter()->addColumn($this->getMainTable(), $columnName, $definition);

        return $this;
    }

    /**
     * Deletes an attribute
     *
     * @param Magento_Customer_Model_Attribute $attribute
     * @return Magento_CustomerCustomAttributes_Model_Resource_Sales_Abstract
     */
    public function deleteAttribute(Magento_Customer_Model_Attribute $attribute)
    {
        $this->_getWriteAdapter()->dropColumn($this->getMainTable(), $this->_getColumnName($attribute));
        return $this;
    }

    /**
     * Return resource model of the main entity
     *
     * @return null
     */
    protected function _getParentResourceModel()
    {
        return null;
    }

    /**
     * Check if main entity exists in main table.
     * Need to prevent errors in case of multiple customer log in into one account.
     *
     * @param Magento_CustomerCustomAttributes_Model_Sales_Abstract $sales
     * @return bool
     */
    public function isEntityExists(Magento_CustomerCustomAttributes_Model_Sales_Abstract $sales)
    {
        if (!$sales->getId()) {
            return false;
        }

        $resource = $this->_getParentResourceModel();
        if (!$resource) {
            /**
             * If resource model is absent, we shouldn't check the database for if main entity exists.
             */
            return true;
        }

        $parentTable = $resource->getMainTable();
        $parentIdField = $resource->getIdFieldName();
        $select = $this->_getWriteAdapter()->select()
            ->from($parentTable, $parentIdField)
            ->forUpdate(true)
            ->where("{$parentIdField} = ?", $sales->getId());
        if ($this->_getWriteAdapter()->fetchOne($select)) {
            return true;
        }
        return false;
    }
}
