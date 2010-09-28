<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @category    Mage
 * @package     Mage_Core
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract resource helper class
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Core_Model_Resource_Helper_Abstract
{
    /**
     * Read adapter instance
     *
     * @var Varien_Db_Adapter_Interface
     */
    protected $_readAdapter;

    /**
     * Write adapter instance
     *
     * @var Varien_Db_Adapter_Interface
     */
    protected $_writeAdapter;

    /**
     * Resource helper module prefix
     *
     * @var string
     */
    protected $_modulePrefix;

    /**
     * Initialize resource helper instance
     *
     * @param string $module
     */
    public function __construct($module)
    {
        $this->_modulePrefix = $module;
    }

    /**
     * Retrieve connection for read data
     *
     * @return Varien_Db_Adapter_Interface
     */
    protected function _getReadAdapter()
    {
        if ($this->_readAdapter === null) {
            $this->_readAdapter = $this->_getConnection('read');
        }

        return $this->_readAdapter;
    }

    /**
     * Retrieve connection for write data
     *
     * @return Varien_Db_Adapter_Interface
     */
    protected function _getWriteAdapter()
    {
        if ($this->_writeAdapter === null) {
            $this->_writeAdapter = $this->_getConnection('write');
        }

        return $this->_writeAdapter;
    }

    /**
     * Create connection to resource
     *
     * @param string $name
     * @return Varien_Db_Adapter_Interface
     */
    protected function _getConnection($name)
    {
        $connection = sprintf('%s_%s', $this->_modulePrefix, $name);
        /* @var $resource Mage_Core_Model_Resource */
        $resource   = Mage::getSingleton('core/resource');

        return $resource->getConnection($connection);
    }

//        public function preapareColumnsList(Varien_Db_Select $select)
//    {
//        if (!count($select->getPart(Zend_Db_Select::COLUMNS)) || !count($select->getPart(Zend_Db_Select::FROM))) {
//            return null;
//        }
//        $tables = $select->getPart(Zend_Db_Select::FROM);
//        $preaparedColumns = array();
//        $columns = $select->getPart(Zend_Db_Select::COLUMNS);
//        foreach ($select->getPart(Zend_Db_Select::COLUMNS) as $columnEntry) {
//            list($correlationName, $column, $alias) = $columnEntry;
//            if($column instanceof Zend_Db_Expr) {
//                if (!is_null($alias)) {
//                    $preaparedColumns[strtoupper($alias)] = array(null, $column, $alias);
//                } else {
//                    throw new Zend_Db_Exception("Cann't preapare expresion without alias");
//                }
//            } else {
//                if ($column == Zend_Db_Select::SQL_WILDCARD) {
//                    foreach(array_keys($this->_getReadAdapter()->describeTable($tables[$correlationName]['tableName'])) as $col) {
//                        $preaparedColumns[strtoupper($col)] = array($correlationName, $col, null);
//                    }
//                } else {
//                    $preaparedColumns[strtoupper(!is_null($alias) ? $alias : $column)] = array(
//                        $correlationName, $column, $alias);
//                }
//            }
//        }
//        $select->reset(Zend_Db_Select::COLUMNS);
//        $select->setPart(Zend_Db_Select::COLUMNS, array_values($preaparedColumns));
//        return $select;
//    }
}
