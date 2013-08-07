<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DB
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Sqlsrv driver DB Statement
 *
 * @category    Magento
 * @package     Magento_DB
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_DB_Statement_Sqlsrv extends Zend_Db_Statement_Sqlsrv
{
    /**
     * Fetches a row from the result set.
     *
     * @param  int $style  OPTIONAL Fetch mode for this fetch operation.
     * @param  int $cursor OPTIONAL Absolute, relative, or other.
     * @param  int $offset OPTIONAL Number for absolute or relative cursors.
     * @return mixed Array, object, or scalar depending on fetch mode.
     * @throws Zend_Db_Statement_Exception
     */
    public function fetch($style = null, $cursor = null, $offset = null)
    {
        try {
            $result = parent::fetch($style, $cursor, $offset);
        } catch (Zend_Db_Statement_Sqlsrv_Exception $e) {
            if ($e->getCode() == -22) {
                return false;
            }
            throw $e;
        }

        if ($result === null) {
            return false;
        }

        return $result;
    }
}
