<?php
#require_once 'Mage/Core/Model/Mysql4.php';

/**
 * Mysql Model for module
 */
class Mage_Core_Model_Mysql4_Resource
{
    protected static $_read = null;
    protected static $_write = null;
    protected static $_resTable = null;
    
    public function __construct()
    {
        self::$_resTable = Mage::registry('resources')->getTableName('core_resource', 'resource');
        self::$_read = Mage::registry('resources')->getConnection('core_read');
        self::$_write = Mage::registry('resources')->getConnection('core_write');
    }
    
    /**
     * Get Module version from DB
     *
     * @param   string $moduleName
     * @return  string
     */
    function getDbVersion($resName)
    {
        if (!self::$_read) {
            return false;
        }
        // if Core module not instaled
        try {
            $select = self::$_read->select()->from(self::$_resTable, 'resource_db_version')
                ->where(self::$_read->quoteInto('resource_name=?', $resName));
            $dbVersion = self::$_read->fetchOne($select);
        }
        catch (Exception $e){
            return false;
        }

        return $dbVersion;
    }

    /**
     * Set module wersion into DB
     *
     * @param   string $moduleName
     * @param   string $version
     * @return  int
     */
    function setDbVersion($resName, $version)
    {
        $dbModuleInfo = array(
            'resource_name'       => $resName,
            'resource_db_version' => $version,
        );
        if ($this -> getDbVersion($resName)) {
        	$condition = self::$_write->quoteInto('resource_name=?', $resName);
        	return self::$_write->update(self::$_resTable, $dbModuleInfo, $condition);
        }
        else {
        	return self::$_write->insert(self::$_resTable, $dbModuleInfo);
        }
    }
}