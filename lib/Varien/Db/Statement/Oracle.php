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
 * @category    Varien
 * @package     Varien_Db
 * @copyright   Copyright (c) 2010 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Oracle DB Statement
 *
 * @category    Varien
 * @package     Varien_Db
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Varien_Db_Statement_Oracle extends Zend_Db_Statement_Oracle
{

    protected $_datetypeMap = array(
            'INTEGER'     => 'integer',
            'NUMBER'      => 'integer',
            'FLOAT'       => 'float',
            'VARCHAR2'    => 'string',
            'TIMESTAMP(6)'=> 'string',
            'LONG'        => 'string',
            'TIMESTAMP'   => 'string',
            'CLOB'        => 'string',
            'BLOB'        => 'string',
            'DATE'        => 'string',
            'VARCHAR2'    => 'string',
        );

    protected $_lobDescriptors = array();
    /**
     * Fetches a row from the result set.
     *
     * @param int $style  OPTIONAL Fetch mode for this fetch operation.
     * @param int $cursor OPTIONAL Absolute, relative, or other.
     * @param int $offset OPTIONAL Number for absolute or relative cursors.
     * @return mixed Array, object, or scalar depending on fetch mode.
     * @throws Zend_Db_Statement_Exception
     */
    public function fetch($style = null, $cursor = null, $offset = null)
    {
        if (!$this->_stmt) {
            return false;
        }

        if ($style === null) {
            $style = $this->_fetchMode;
        }

        $lob_as_string = $this->getLobAsString() ? OCI_RETURN_LOBS : 0;

        switch ($style) {
            case Zend_Db::FETCH_NUM:
                $row = $this->_typedFetch($style, OCI_NUM | OCI_RETURN_NULLS | $lob_as_string);
                break;
            case Zend_Db::FETCH_ASSOC:
                $row = $this->_typedFetch($style, OCI_ASSOC | OCI_RETURN_NULLS | $lob_as_string);
                break;
            case Zend_Db::FETCH_BOTH:
                $row = $this->_typedFetch($style, OCI_BOTH | OCI_RETURN_NULLS | $lob_as_string);
                break;
            case Zend_Db::FETCH_OBJ:
                $row = oci_fetch_object($this->_stmt);
                break;
            case Zend_Db::FETCH_BOUND:
                $row = $this->_typedFetch($style, OCI_BOTH | OCI_RETURN_NULLS | $lob_as_string);
                if ($row !== false) {
                    return $this->_fetchBound($row);
                }
                break;
            default:
                /**
                 * @see Zend_Db_Adapter_Oracle_Exception
                 */
                #require_once 'Zend/Db/Statement/Oracle/Exception.php';
                throw new Zend_Db_Statement_Oracle_Exception(
                    array(
                        'code'    => 'HYC00',
                        'message' => "Invalid fetch mode '$style' specified"
                    )
                );
                break;
        }

        if (! $row && $error = oci_error($this->_stmt)) {
            /**
             * @see Zend_Db_Adapter_Oracle_Exception
             */
            #require_once 'Zend/Db/Statement/Oracle/Exception.php';
            throw new Zend_Db_Statement_Oracle_Exception($error);
        }

        if (is_array($row) && array_key_exists('zend_db_rownum', $row)) {
            unset($row['zend_db_rownum']);
        }
        return $row;
    }


    /**
     * Returns an array containing all of the result set rows.
     *
     * @param int $style OPTIONAL Fetch mode.
     * @param int $col   OPTIONAL Column number, if fetch mode is by column.
     * @return array Collection of rows, each in a format by the fetch mode.
     * @throws Zend_Db_Statement_Exception
     */
    public function fetchAll($style = null, $col = 0)
    {
        if (!$this->_stmt) {
            return false;
        }

        // make sure we have a fetch mode
        if ($style === null) {
            $style = $this->_fetchMode;
        }

        $ncols = oci_num_fields($this->_stmt);

        $columns_datatype = array();

        if ($style != Zend_Db::FETCH_NUM) {
            for ($i = 1; $i <= $ncols; $i++) {

//                var_dump(
//                    oci_field_name($this->_stmt, $i),
//                    oci_field_type($this->_stmt, $i),
//                    //oci_field_size($this->_stmt, $i),
//                    oci_field_scale($this->_stmt, $i),
//                    oci_field_precision($this->_stmt, $i));


                if (oci_field_type($this->_stmt, $i) === 'NUMBER' && oci_field_scale($this->_stmt, $i) !== 0
                    /* && ( oci_field_scale($this->_stmt, $i) !== 0 || oci_field_precision($this->_stmt, $i) === 0)*/) {
                    $columns_datatype[oci_field_name($this->_stmt, $i)] = 'FLOAT';
                } else {
                    $columns_datatype[oci_field_name($this->_stmt, $i)] = 'STRING';//$this->_datetypeMap[oci_field_type($this->_stmt, $i)];
                }
            }

        } else {
            for ($i = 1; $i <= $ncols; $i++) {
//
//                var_dump(
//                    oci_field_name($this->_stmt, $i),
//                    oci_field_type($this->_stmt, $i),
//                    oci_field_scale($this->_stmt, $i),
//                    oci_field_precision($this->_stmt, $i));
//
                if (oci_field_type($this->_stmt, $i) === 'NUMBER' && oci_field_scale($this->_stmt, $i) !== 0) {
                    $columns_datatype[$i - 1] = 'FLOAT';

                } else {
                    $columns_datatype[$i - 1] = 'STRING'; //$this->_datetypeMap[oci_field_type($this->_stmt, $i)];
                }
            }
        }

        $flags = OCI_FETCHSTATEMENT_BY_ROW;

        switch ($style) {
            case Zend_Db::FETCH_BOTH:
                /**
                 * @see Zend_Db_Adapter_Oracle_Exception
                 */
                #require_once 'Zend/Db/Statement/Oracle/Exception.php';
                throw new Zend_Db_Statement_Oracle_Exception(
                    array(
                        'code'    => 'HYC00',
                        'message' => "OCI8 driver does not support fetchAll(FETCH_BOTH), use fetch() in a loop instead"
                    )
                );
                // notreached
                $flags |= OCI_NUM;
                $flags |= OCI_ASSOC;
                break;
            case Zend_Db::FETCH_NUM:
                $flags |= OCI_NUM;
                break;
            case Zend_Db::FETCH_ASSOC:
                $flags |= OCI_ASSOC;
                break;
            case Zend_Db::FETCH_OBJ:
                break;
            case Zend_Db::FETCH_COLUMN:
                $flags = $flags &~ OCI_FETCHSTATEMENT_BY_ROW;
                $flags |= OCI_FETCHSTATEMENT_BY_COLUMN;
                $flags |= OCI_NUM;
                break;
            default:
                /**
                 * @see Zend_Db_Adapter_Oracle_Exception
                 */
                #require_once 'Zend/Db/Statement/Oracle/Exception.php';
                throw new Zend_Db_Statement_Oracle_Exception(
                    array(
                        'code'    => 'HYC00',
                        'message' => "Invalid fetch mode '$style' specified"
                    )
                );
                break;
        }

        $result = Array();

        if ( $flags != OCI_FETCHSTATEMENT_BY_ROW)

        { /* not Zend_Db::FETCH_OBJ */
            if (! ($rows = oci_fetch_all($this->_stmt, $result, 0, -1, $flags) )) {
                if ($error = oci_error($this->_stmt)) {
                    /**
                     * @see Zend_Db_Adapter_Oracle_Exception
                     */
                    #require_once 'Zend/Db/Statement/Oracle/Exception.php';
                    throw new Zend_Db_Statement_Oracle_Exception($error);
                }
                if (!$rows) {
                    return array();
                }
            }
            if ($style == Zend_Db::FETCH_COLUMN) {
                $result = $result[$col];
            }
            foreach ($result as $k => &$row) {

                if (is_array($row)) {
                    $result[$k] = array_change_key_case($row, CASE_LOWER);
                }
                if (is_array($row) && array_key_exists('zend_db_rownum', $row)) {
                    unset($row['zend_db_rownum']);
                }
                if (is_array($row)){
                    $datarow = array();
                    foreach ($row as $key => $item) {
                        if (!is_null($item)) {
                            settype($item, $columns_datatype[strtoupper($key)]);
                        }
                        $datarow[strtolower($key)] = $item;
                    }
                    $result[$k] = $datarow;
                }
            }
        }
        else {
            while (($row = oci_fetch_object($this->_stmt)) !== false) {
                if ($row){
                    $datarow = array();
                    foreach ($row as $key => $item) {
                        if(!oci_field_is_null($this->_stmt, $key)) {
                            settype($item, $columns_datatype[$key]);
                        }
                        $datarow[strtolower($key)] = $item;
                    }
                    $result [] = $datarow;
                } else {
                    $result [] = $row;
                }
            }
            if ($error = oci_error($this->_stmt)) {
                /**
                 * @see Zend_Db_Adapter_Oracle_Exception
                 */
                #require_once 'Zend/Db/Statement/Oracle/Exception.php';
                throw new Zend_Db_Statement_Oracle_Exception($error);
            }
        }
        return $result;
    }

    protected function _typedFetch($style, $fetchMode)
    {
        $ncols = oci_num_fields($this->_stmt);

        $columns_datatype = array();

        if ($style != Zend_Db::FETCH_NUM) {
            for ($i = 1; $i <= $ncols; $i++) {

                if (oci_field_type($this->_stmt, $i) === 'NUMBER' && oci_field_scale($this->_stmt, $i) !== 0) {
                    $columns_datatype[oci_field_name($this->_stmt, $i)] = 'FLOAT';

                } else {
                    $columns_datatype[oci_field_name($this->_stmt, $i)] = 'STRING';//$this->_datetypeMap[oci_field_type($this->_stmt, $i)];
                }
            }
        } else {
            for ($i = 1; $i <= $ncols; $i++) {
               if (oci_field_type($this->_stmt, $i) === 'NUMBER' && oci_field_scale($this->_stmt, $i) !== 0) {
                    $columns_datatype[$i - 1] = 'FLOAT';

                } else {
                    $columns_datatype[$i - 1] = 'STRING';//$this->_datetypeMap[oci_field_type($this->_stmt, $i)];
                }
            }
        }

        $row = oci_fetch_array($this->_stmt, $fetchMode);

        if ($row){
            $datarow = array();
            foreach ($row as $key => $item) {
                if(!is_null($item)) {
                    settype($item, $columns_datatype[$key]);
                }
                $datarow[strtolower($key)] = $item;
            }
            $row = $datarow;
        }

        return $row;
    }

    /**
     * Executes a prepared statement.
     *
     * @param array $params OPTIONAL Values to bind to parameter placeholders.
     * @return bool
     * @throws Zend_Db_Statement_Exception
     */
    public function _execute(array $params = null)
    {
        $connection = $this->_adapter->getConnection();

        if (!$this->_stmt) {
            return false;
        }

        if ($params !== null) {
            if (!is_array($params)) {
                $params = array($params);
            }
            $error = false;
            foreach (array_keys($params) as $name) {
                if (strlen($params[$name]) > 4000) {
                    $this->_lobDescriptors[$name] = oci_new_descriptor($connection);
                    if (!@oci_bind_by_name($this->_stmt, $name, $this->_lobDescriptors[$name], -1, OCI_B_CLOB)) {
                        $error = true;
                        break;
                    }
                    $this->_lobDescriptors[$name]->writeTemporary($params[$name], OCI_TEMP_CLOB);
                } else {
                    if (!@oci_bind_by_name($this->_stmt, $name, $params[$name], -1)) {
                        $error = true;
                        break;
                    }
                }
            }
            if ($error) {
                /**
                 * @see Zend_Db_Adapter_Oracle_Exception
                 */
                #require_once 'Zend/Db/Statement/Oracle/Exception.php';
                throw new Zend_Db_Statement_Oracle_Exception(oci_error($this->_stmt));
            }
        }

        $retval = @oci_execute($this->_stmt, $this->_adapter->_getExecuteMode());
        if ($retval === false) {
            /**
             * @see Zend_Db_Adapter_Oracle_Exception
             */
            #require_once 'Zend/Db/Statement/Oracle/Exception.php';
            throw new Zend_Db_Statement_Oracle_Exception(oci_error($this->_stmt));
        }

        $this->_keys = Array();
        if ($field_num = oci_num_fields($this->_stmt)) {
            for ($i = 1; $i <= $field_num; $i++) {
                $name = oci_field_name($this->_stmt, $i);
                $this->_keys[] = $name;
            }
        }

        $this->_values = Array();
        if ($this->_keys) {
            $this->_values = array_fill(0, count($this->_keys), null);
        }

        return $retval;
    }

    /**
     * Closes the cursor, allowing the statement to be executed again.
     *
     * @return bool
     */
    public function closeCursor()
    {
        foreach ($this->_lobDescriptors as $descriptor) {
            $descriptor->close();
        }
        return parent::closeCursor();
    }
}
