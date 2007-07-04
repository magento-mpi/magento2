<?php
#require_once 'Mage/Core/Model/Mysql4.php';

/**
 * Mysql Model for module
 */
class Mage_Core_Model_Mysql4_Resource
{
    protected $_read = null;
    protected $_write = null;
    protected $_resTable = null;
    
    public function __construct()
    {
        $this->_resTable = Mage::getSingleton('core/resource')->getTableName('core/resource');
        $this->_read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('core_write');
    }
    
    /**
     * Get Module version from DB
     *
     * @param   string $moduleName
     * @return  string
     */
    function getDbVersion($resName)
    {
        if (!$this->_read) {
            return false;
        }
        // if Core module not instaled
        try {
            $select = $this->_read->select()->from($this->_resTable, 'version')
                ->where('code=?', $resName);
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
        $dbModuleInfo = array(
            'code'    => $resName,
            'version' => $version,
        );
        if ($this -> getDbVersion($resName)) {
        	$condition = $this->_write->quoteInto('code=?', $resName);
        	return $this->_write->update($this->_resTable, $dbModuleInfo, $condition);
        }
        else {
        	return $this->_write->insert($this->_resTable, $dbModuleInfo);
        }
    }
}