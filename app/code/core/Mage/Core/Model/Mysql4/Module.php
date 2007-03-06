<?php
#require_once 'Ecom/Core/Model/Mysql4.php';

/**
 * Mysql Model for module
 */
class Mage_Core_Model_Mysql4_Module extends Mage_Core_Model_Mysql4
{
    /**
     * Get Module version from DB
     *
     * @param   string $moduleName
     * @return  string
     */
    function getDbVersion($moduleName)
    {
        $moduleTable = $this->_getTableName('core_read', 'module');

        // if Core module not instaled
        try {
            $dbVersion = $this->_read->fetchOne(
                "select module_db_version from ".$moduleTable." where module_name=?"
               , array($moduleName)
            );
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
    function setDbVersion($moduleName, $version)
    {
        $moduleTable = $this->_getTableName('core_write', 'module');

        $dbModuleInfo = array(
            'module_db_version' => $version
           ,'module_name'       => $moduleName
        );
        if ($this -> getDbVersion($moduleName)) {
        	$condition = $this->_write->quoteInto('module_name=?', $moduleName);
        	return $this->_write->update($moduleTable, $dbModuleInfo, $condition);
        }
        else {
        	return $this->_write->insert($moduleTable, $dbModuleInfo);
        }
    }
}