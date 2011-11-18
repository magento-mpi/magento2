<?php
/**
 * {license_notice}
 *
 * @category   Mage
 * @package    Mage_LoadTest
 * @copyright  {copyright}
 * @license    {license_link}
 */


class Mage_LoadTest_Model_Resource_Type_Db_Pdo_Mysql extends Mage_Core_Model_Resource_Type_Db_Pdo_Mysql
{

    /**
     * Retrieve DB adapter class name
     *
     * @return string
     */
    protected function _getDbAdapterClassName()
    {
        return 'Mage_LoadTest_Model_Db_Adapter_Pdo_Mysql';
    }

}
