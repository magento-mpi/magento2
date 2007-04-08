<?php
#require_once 'Mage/Core/Model/Mysql4.php';

/**
 * Mysql Model for module
 */
class Mage_Core_Resource_Model_Mysql4_Resource extends Mage_Core_Resource_Model_Mysql4
{
    /**
     * Get Module version from DB
     *
     * @param   string $moduleName
     * @return  string
     */
    function getDbVersion($resName)
    {
        $resTable = Mage::registry('resources')->getTableName('core', 'resource');

        // if Core module not instaled
        try {
            $select = $this->_read->select()->from($resTable)
                ->where($this->_read->quoteInto('resource_name=?', $resName));
            $dbVersion = $this->_read->fetchOne($select);
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
        $resTable = Mage::registry('resources')->getTableName('core', 'resource');

        $dbModuleInfo = array(
            'resource_name'       => $resName,
            'resource_db_version' => $version,
        );
        if ($this -> getDbVersion($resName)) {
        	$condition = $this->_write->quoteInto('resource_name=?', $resName);
        	return $this->_write->update($resTable, $dbModuleInfo, $condition);
        }
        else {
        	return $this->_write->insert($resTable, $dbModuleInfo);
        }
    }
}