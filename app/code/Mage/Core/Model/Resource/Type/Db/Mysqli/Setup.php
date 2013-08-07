<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Core_Model_Resource_Type_Db_Mysqli_Setup extends Mage_Core_Model_Resource_Type_Db
{
    /**
     * Get connection
     * 
     * @param Array $config
     * @return Magento_DB_Adapter_Mysqli
     */
    public function getConnection($config)
    {
        $conn = new Magento_DB_Adapter_Mysqli((array)$config);

        return $conn;
    }
}
