<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_Customer
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Customer Sales Mysql4 abstract resource
 *
 */
abstract class Enterprise_Customer_Model_Mysql4_Sales_Abstract extends Enterprise_Enterprise_Model_Core_Mysql4_Abstract
{
    /**
     * Used us prefix to name of column table
     *
     * @var null | string
     */
    protected $_columnPrefix        = 'customer';

    /**
     * Primery key auto increment flag
     *
     * @var bool
     */
    protected $_isPkAutoIncrement   = false;

    /**
     * Return column name for attribute
     *
     * @param Mage_Customer_Model_Attribute $attribute
     * @return string
     */
    protected function _getColumnName(Mage_Customer_Model_Attribute $attribute)
    {
        if ($this->_columnPrefix) {
            return sprintf('%s_%s', $this->_columnPrefix, $attribute->getAttributeCode());
        }
        return $attribute->getAttributeCode();
    }

    /**
     * Saves a new attribute
     *
     * @param Mage_Customer_Model_Attribute $attribute
     * @return Enterprise_Customer_Model_Mysql4_Sales_Abstract
     */
    public function saveNewAttribute(Mage_Customer_Model_Attribute $attribute)
    {
        $backendType = $attribute->getBackendType();
        if ($backendType == Mage_Customer_Model_Attribute::TYPE_STATIC) {
            return $this;
        }

        switch ($backendType) {
            case 'datetime':
                $defination = "DATE NULL DEFAULT NULL";
                break;
            case 'decimal':
                $defination = "DECIMAL(12,4) NOT NULL DEFAULT '0.0000'";
                break;
            case 'int':
                $defination = "INT(11) NOT NULL DEFAULT '0'";
                break;
            case 'text':
                $defination = "TEXT NOT NULL";
                break;
            case 'varchar':
                $defination = "VARCHAR(255) NOT NULL DEFAULT ''";
                break;
            default:
                return $this;
        }

        $this->_getWriteAdapter()->addColumn($this->getMainTable(), $this->_getColumnName($attribute), $defination);

        return $this;
    }

    /**
     * Deletes an attribute
     *
     * @param Mage_Customer_Model_Attribute $attribute
     * @return Enterprise_Customer_Model_Mysql4_Sales_Abstract
     */
    public function deleteAttribute(Mage_Customer_Model_Attribute $attribute)
    {
        $this->_getWriteAdapter()->dropColumn($this->getMainTable(), $this->_getColumnName($attribute));
        return $this;
    }

    /**
     * CopyFieldset converts customer attributes from source object to target object
     *
     * @param Mage_Core_Model_Abstract $source
     * @param Mage_Core_Model_Abstract $target
     * @param array $fields
     * @param bool $useColumnPrefix
     * @return Enterprise_Customer_Model_Sales_Abstract
     */
    public function copyFieldsetSourceToTarget(
        Mage_Core_Model_Abstract $source,
        Mage_Core_Model_Abstract $target,
        array                    $fields = null,
                                 $useColumnPrefix = false
    ){
        if ($fields === null) {
            $fields = $this->describeTable();
        }
        unset($fields[$this->getIdFieldName()]);
        if ($useColumnPrefix) {
            foreach ($fields as $key => $data ) {
                $keyCut = substr($key, strlen($this->_columnPrefix) + 1, strlen($key));
                if ($source->hasData($keyCut)) {
                    $target->setData($key, $source->getData($keyCut));
                }
            }
        } else {
            $sourceIntersect = array_intersect_key($source->getData(), $fields);
            $target->addData($sourceIntersect);
        }

        return $this;
    }

    /**
     * Describe table
     *
     * @return array
     */
    public function describeTable()
    {
        return $this->_getReadAdapter()->describeTable($this->getMainTable());
    }
}
