<?php

/**
 * Mysql4 session save handler
 *
 * @copyright   2007 Varien Inc.
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @package     Mage
 * @subpackage  Core
 * @author      Moshe Gurvich <moshe@varien.com>
 */
class Mage_Core_Model_Mysql4_Session implements Zend_Session_SaveHandler_Interface 
{
    /**
     * Session lifetime
     * 
     * @var integer
     */
    protected $_lifeTime;
    
    /**
     * Session data table name
     *
     * @var string
     */
    protected $_sessionTable;

    /**
     * Database read connection
     * 
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_read;
    
    /**
     * Database write connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_write;
    
    public function __construct()
    {
        $this->_sessionTable = Mage::getSingleton('core/resource')->getTableName('core/session');
        $this->_read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('core_write');
    }
    
    /**
     * Check DB connection
     *
     * @return bool
     */
    public function hasConnection()
    {
        if (!$this->_read) {
            return false;
        }
        $tables = $this->_read->fetchAssoc('show tables');
        if (!isset($tables[$this->_sessionTable])) {
            return false;
        }
        
        return true;
    }
    
    public function setSaveHandler()
    {
        if ($this->hasConnection()) {
            session_set_save_handler(
                array($this, 'open'), 
                array($this, 'close'), 
                array($this, 'read'),
                array($this, 'write'),
                array($this, 'destroy'),
                array($this, 'gc')
            );
        } else {
            session_save_path(Mage::getBaseDir('session'));
        }
        return $this;
    }
    
    /**
     * Open session
     *
     * @param string $savePath ignored
     * @param string $sessName ignored
     * @return boolean
     */
    public function open($savePath, $sessName) 
    {
        $this->_lifeTime = get_cfg_var('session.gc_maxlifetime');
        return true;
    }
    
    /**
     * Close session
     *
     * @return boolean
     */
    public function close() 
    {
        $this->gc(ini_get('session.gc_maxlifetime'));
        
        return true;
    }

    /**
     * Fetch session data
     *
     * @param string $sessId
     * @return string
     */
    public function read($sessId) 
    {
        $data = $this->_read->fetchOne(
            "SELECT session_data FROM $this->_sessionTable
             WHERE session_id = ? AND session_expires > ?", 
            array($sessId, time())
        );
        
        return $data;
    }
    
    /**
     * Update session
     *
     * @param string $sessId
     * @param string $sessData
     * @return boolean
     */
    public function write($sessId, $sessData) 
    {
        $bind = array(
            'session_expires'=>time()+$this->_lifeTime, 
            'session_data'=>$sessData
        );
        
        $exists = $this->_write->fetchOne(
            "SELECT session_id FROM $this->_sessionTable 
             WHERE session_id = ?", array($sessId)
        );
        
        if ($exists) {
            $where = $this->_write->quoteInto('session_id=?', $sessId);
            $this->_write->update($this->_sessionTable, $bind, $where);
        } else {
            $bind['session_id'] = $sessId;
            $this->_write->insert($this->_sessionTable, $bind);
        }
        
        return true;
    }

    /**
     * Destroy session
     *
     * @param string $sessId
     * @return boolean
     */
    public function destroy($sessId) 
    {
        $this->_write->query("DELETE FROM $this->_sessionTable WHERE session_id = ?", array($sessId));
        return true;
    }
    
    /**
     * Garbage collection
     *
     * @param int $sessMaxLifeTime ignored
     * @return boolean
     */
    public function gc($sessMaxLifeTime) 
    {
        $this->_write->query("DELETE FROM $this->_sessionTable WHERE session_expires < ?", array(time()));
        return true;
    }
}
