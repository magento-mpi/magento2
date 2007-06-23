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
        self::$_resTable = Mage::getSingleton('core/resource')->getTableName('core/resource');
        self::$_read = Mage::getSingleton('core/resource')->getConnection('core_read');
        self::$_write = Mage::getSingleton('core/resource')->getConnection('core_write');
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
            $select = self::$_read->select()->from(self::$_resTable, 'version')
                ->where(self::$_read->quoteInto('code=?', $resName));
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
            'code'    => $resName,
            'version' => $version,
        );
        if ($this -> getDbVersion($resName)) {
        	$condition = self::$_write->quoteInto('code=?', $resName);
        	return self::$_write->update(self::$_resTable, $dbModuleInfo, $condition);
        }
        else {
        	return self::$_write->insert(self::$_resTable, $dbModuleInfo);
        }
    }
}