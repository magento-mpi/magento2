<?php
/**
 * Test framework custom connection adapter
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_TestFramework_Db_ConnectionAdapter extends Magento_Core_Model_Resource_Type_Db_Pdo_Mysql
{
    /**
     * Retrieve DB adapter class name
     *
     * @return string
     */
    protected function _getDbAdapterClassName()
    {
        return 'Magento_Test_Db_Adapter_Mysql';
    }
}
