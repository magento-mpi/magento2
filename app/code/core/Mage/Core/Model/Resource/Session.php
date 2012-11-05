<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Session save handler
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Resource_Session implements Zend_Session_SaveHandler_Interface
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
     * @var Varien_Db_Adapter_Interface
     */
    protected $_read;

    /**
     * Database write connection
     *
     * @var Varien_Db_Adapter_Interface
     */
    protected $_write;

    /**
     * Constructor
     *
     * @param Mage_Core_Model_Resource $resource
     */
    public function __construct(Mage_Core_Model_Resource $resource)
    {
        $this->_sessionTable = $resource->getTableName('core_session');
        $this->_read         = $resource->getConnection('core_read');
        $this->_write        = $resource->getConnection('core_write');
    }

    /**
     * Destrucor
     *
     */
    public function __destruct()
    {
        session_write_close();
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
        if (!$this->_read->isTableExists($this->_sessionTable)) {
            return false;
        }

        return true;
    }

    /**
     * Setup save handler
     *
     * @return Mage_Core_Model_Resource_Session
     */
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
        return true;
    }

    /**
     * Close session
     *
     * @return boolean
     */
    public function close()
    {
        return true;
    }

    /**
     * Fetch session data
     *
     * @param string $sessionId
     * @return string
     */
    public function read($sessionId)
    {
        $select = $this->_read->select()
            ->from($this->_sessionTable, array('session_data'))
            ->where('session_id = :session_id');
        $bind = array(
            'session_id' => $sessionId,
        );

        $data = $this->_read->fetchOne($select, $bind);
        return base64_decode($data);
    }

    /**
     * Update session
     *
     * @param string $sessionId
     * @param string $sessionData
     * @return boolean
     */
    public function write($sessionId, $sessionData)
    {
        $bindValues = array('session_id' => $sessionId);
        $select = $this->_read->select()
            ->from($this->_sessionTable)
            ->where('session_id = :session_id');
        $exists = $this->_read->fetchOne($select, $bindValues);

        // encode session serialized data to prevent insertion of incorrect symbols
        $bind = array(
            'session_expires' => time(),
            'session_data'    => base64_encode($sessionData),
        );

        if ($exists) {
            $this->_write->update($this->_sessionTable, $bind, array('session_id=?' => $sessionId));
        } else {
            $bind['session_id'] = $sessionId;
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
        $where = array('session_id = ?' => $sessId);
        $this->_write->delete($this->_sessionTable, $where);
        return true;
    }

    /**
     * Garbage collection
     *
     * @param int $maxLifeTime
     * @return boolean
     */
    public function gc($maxLifeTime)
    {
        $where = array('session_expires < ?' => time() - $maxLifeTime);
        $this->_write->delete($this->_sessionTable, $where);
        return true;
    }
}
