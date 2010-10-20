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
 * @package     Enterprise_Staging
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Eav Mssql resource helper model
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class  Enterprise_Staging_Model_Resource_Helper_Mssql extends Mage_Eav_Model_Resource_Helper_Mssql
{
    /**
     * Join information for last staging logs
     *
     * @param  string $table
     * @param  Varien_Db_Select $select
     * @return Varien_Db_Select $select
     */
    public function getLastStagingLogQuery($table, $select)
    {
        $subSelect =  clone $select;
        $subSelect->from($table, array('staging_id', 'log_id', 'action'))
            ->columns('RANK() OVER (PARTITION BY staging_id ORDER BY log_id DESC) as order_log_id');

        $select->from(array('t' => new Zend_Db_Expr('(' . $subSelect . ')')))
            ->where('t.order_log_id = 1');

        return $select;
    }

    /**
     * Returns Ddl Column info from native Db format
     * @param  $field
     * @return array
     */
    public function getDdlInfoByDescription($field)
    {
        $columnName = $field['COLUMN_NAME'];
        $ddlOptions = array();
        $ddlSize = $ddlType = null;
        switch ($field['DATA_TYPE']) {
            case 'int':
                $ddlType = Varien_Db_Ddl_Table::TYPE_INTEGER;
                break;
            case 'bigint':
                $ddlType = Varien_Db_Ddl_Table::TYPE_BIGINT;
                break;
            case 'boolean':
                $ddlType = Varien_Db_Ddl_Table::TYPE_BOOLEAN;
                break;
            case 'float':
                $ddlType = Varien_Db_Ddl_Table::TYPE_FLOAT;
                break;
            case 'decimal':
                $ddlType = Varien_Db_Ddl_Table::TYPE_DECIMAL;
                $ddlSize = $field['PRECISION'] . ',' .$field['SCALE'];
                break;
            case 'datetime':
                $ddlType = Varien_Db_Ddl_Table::TYPE_TIMESTAMP;
                break;
            case 'varchar':
            case 'text':
                $ddlType = Varien_Db_Ddl_Table::TYPE_TEXT;
                $ddlSize = $field['LENGTH'];
                break;
            default:
                break;
        }

        if ($field['UNSIGNED']) {
            $ddlOptions['unsigned'] = true;
        }
        if (!$field['NULLABLE']) {
            $ddlOptions['nullable'] = false;
        }
        if ($field['IDENTITY']) {
            $ddlOptions['identity'] = true;
        }
        if ($field['PRIMARY']) {
            $ddlOptions['primary'] = true;
        }
        if ($field['DEFAULT']) {
            $ddlOptions['default'] = $field['DEFAULT'];
        }

        return array($columnName, $ddlType, $ddlSize, $ddlOptions);
    }
}
