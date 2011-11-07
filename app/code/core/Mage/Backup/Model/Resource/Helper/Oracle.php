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
 * @package     Mage_Backup
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Backup_Model_Resource_Helper_Oracle extends Mage_Core_Model_Resource_Helper_Oracle
{

    protected $_dateTypeMap  = array(
        'NUMBER'    => array(
            'FLOAT','NUMBER','ROWID'),
        'STRING'    => array(
            'CLOB','CHAR','VARCHAR2','BLOB'),
        'DATETIME'  => array(
            'DATE','TIMESTAMP(6)')
    );

    /**
     * Transform params for DBMS_METADATA
     */
    protected function _transformDdlParams()
    {
        $this->_getWriteAdapter()->query(
                "BEGIN\n"
                ."dbms_metadata.set_transform_param(dbms_metadata.session_transform, 'SQLTERMINATOR', true);\n"
                ."dbms_metadata.set_transform_param(dbms_metadata.session_transform, 'STORAGE', false);\n"
                ."dbms_metadata.set_transform_param(dbms_metadata.session_transform, 'TABLESPACE', false);\n"
                ."dbms_metadata.set_transform_param(dbms_metadata.session_transform, 'CONSTRAINTS', true);\n"
                ."dbms_metadata.set_transform_param(dbms_metadata.session_transform, 'REF_CONSTRAINTS', false);\n"
                ."dbms_metadata.set_transform_param(dbms_metadata.session_transform, 'SEGMENT_ATTRIBUTES', false);\n"
                ."END;");
    }

    /**
     * Retrieve SQL fragment for drop table
     *
     * @param string $tableName
     * @return string
     */
    public function getTableDropSql($tableName)
    {
        $quotedTableName = $this->_getReadAdapter()->quoteIdentifier($tableName);
        $dropTableSql = sprintf("
            declare
                v_is_table_exists number;
            begin
                select count(1)
                into v_is_table_exists
                from user_tables t
                where t.table_name = '%s';

                if (v_is_table_exists > 0) then
                    execute immediate 'DROP TABLE ' || '%s';
                end if;
            end;
            / \n", $quotedTableName, $quotedTableName
        );
        return $dropTableSql;
    }

    /**
     * Retrieve foreign keys for table
     *
     * @param string|null $tableName
     * @return string
     */
    public function getTableForeignKeysSql($tableName)
    {
        $adapter    = $this->_getReadAdapter();
        $selFkCount = $adapter->select();
        $selFkCount->from('user_constraints', array('COUNT(1)'))
            ->where('table_name = ?', $tableName)
            ->where("constraint_type = 'R'");

        $fkScript = '';
        $fkCount  = $adapter->fetchOne($selFkCount);
        if ($fkCount > 0){
            $selFkDdl = $adapter->select();
            $selFkDdl->from('dual',  array(
                    new Zend_Db_Expr("DBMS_METADATA.GET_DEPENDENT_DDL('REF_CONSTRAINT', :table_name, USER)")));

            $fkScript = $adapter->fetchOne($selFkDdl,array('table_name' => $tableName));
        }

        return $fkScript;
    }

    /**
     * Retrieve table ddl script
     *
     * @param string $tableName
     * @return string
     */
    protected function _getTableDdl($tableName) {
        $select = $this->_getReadAdapter()->select();
        $select->from('dual', array(new Zend_Db_Expr("DBMS_METADATA.get_ddl('TABLE', :table_name, USER)")));
        return $this->_getReadAdapter()->fetchOne($select, array('table_name' => $tableName));
    }

    /**
     * Get DDL script for create table
     *
     * @param string $tableName
     * @param boolean $addDropIfExists
     * @return string
     */
    public function getTableCreateScript($tableName, $addDropIfExists = false)
    {
        $script = '';
        if($addDropIfExists) {
            $script = $this->getTableDropSql($tableName);
        }
        $script .= $this->_getTableDdl($tableName);

        return $script;
    }

    /**
     * Retrieve SQL fragment for create table
     *
     * @param string $tableName
     * @param bool $withForeignKeys
     * @return string
     */
    public function getTableCreateSql($tableName, $withForeignKeys = false)
    {
        if ($withForeignKeys) {
            $this->_getReadAdapter()->query(
                "BEGIN\n"
                ."dbms_metadata.set_transform_param(dbms_metadata.session_transform, 'REF_CONSTRAINTS', true);\n"
                ."END;");
        }
        return $this->_getTableDdl($tableName);
    }

    /**
     * Return scripts for sequences
     *
     * @return string
     */
    protected function _getSequencesDefinition()
    {
        $script = '';
        $query   = $this->_getReadAdapter()->query("
            select dbms_metadata.get_ddl('SEQUENCE', s.sequence_name)
            from user_sequences s ");
        while ($row = $query->fetchColumn()) {
            $script .= $row;
        }

        return $script;
    }

     /**
     * Return scripts for sequences
     *
     * @return string
     */
    protected function _getUserDefinition()
    {
        $script = '';
        $query = $this->_getReadAdapter()->query("
            select dbms_metadata.get_ddl('SEQUENCE', s.sequence_name)
            from user_sequences s ");
        while ($row = $query->fetchColumn()) {
            $script .= $row;
        }

        return $script;
    }

    /**
     * Return scripts for procedures and functions
     *
     * @return string
     */
    protected function _getProgObjectsDefinition()
    {
        $script = '';
        $select = $this->_getReadAdapter()->select();
        $select->from(
            array(
                'up' => 'user_procedures',
                array(new Zend_Db_Expr('DBMS_METADATA.GET_DDL(uo.object_type, uo.object_name)')))
            )
            ->join(array('uo' => 'user_objects'), 'up.object_name = uo.object_name', array())
            ->where('uo.object_type NOT IN (?)', array('PACKAGE BODY','TYPE BODY'));
        $query = $this->_getReadAdapter()->query($select);

        while ($row = $query->fetchColumn()) {
            $script .= $row;
        }

        return $script;
    }


    /**
     * Returns SQL header data, move from original resource model
     *
     * @return string
     */
    public function getHeader()
    {
        $this->_transformDdlParams();
        $this->_getReadAdapter()->query('PURGE RECYCLEBIN');
        $header = "ALTER SESSION SET NLS_TIMESTAMP_FORMAT = 'YYYY-MM-DD HH24:MI:SS'\n"
            . "ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD HH24:MI:SS'\n";
        $header .= $this->_getSequencesDefinition();

        return $header;
    }

    /**
     * Returns SQL footer data, move from original resource model
     *
     * @return string
     */
    public function getFooter()
    {
        return $this->_getProgObjectsDefinition();
    }

    /**
     * Retrieve before insert data SQL fragment
     *
     * @param string $tableName
     * @return string
     */
    public function getTableDataBeforeSql($tableName)
    {
        return '';
    }

    /**
     * Retrieve after insert data SQL fragment
     *
     * @param string $tableName
     * @return string
     */
    public function getTableDataAfterSql($tableName)
    {
        return '';
    }

    /**
     * Return insert query
     *
     * @param  $tableName
     * @return string
     */
    public function getInsertSql($tableName)
    {
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()->from($tableName);
        $query   = $adapter->query($select);
        $insert  = '';
        $columns = $adapter->describeTable($tableName);
        while ($row = $query->fetch(Zend_Db::FETCH_ASSOC)) {
            $insRowData = array();
            foreach ($row as $key => $value) {
                if ($value === null) {
                    $insRowData[$key] = 'NULL';
                } else {
                    if (in_array($columns[$key]['DATA_TYPE'], $this->_dateTypeMap['STRING'])) {
                        $insRowData[$key] = sprintf("'%s'", str_replace("'", "''", $value) );
                    } elseif (!(array_search($columns[$key]['DATA_TYPE'], $this->_dateTypeMap['DATETIME']) === false) ) {
                        $insRowData[$key] = sprintf("TO_DATE('%s', 'YYYY-MM-DD HH24:MI:SS')",$value);
                    } else {
                        $insRowData[$key] = $value;
                    }
                }
            }
            $insert .= sprintf("INSERT INTO %s (%s) VALUES (%s);\n",
                $tableName,
                implode(',', array_keys($columns)),
                implode(',', $insRowData));
        }

        return $insert;
    }

    /**
     * Turn on serializable mode
     */
    public function turnOnSerializableMode()
    {
        $this->_getReadAdapter()->query(" ALTER SESSION SET ISOLATION_LEVEL = SERIALIZABLE");
    }

    /**
     * Turn on read committed mode
     */
    public function turnOnReadCommittedMode()
    {
        $this->_getReadAdapter()->query("ALTER SESSION SET ISOLATION_LEVEL = READ COMMITTED");
    }
}
