<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Session\SaveHandler;

/**
 * Data base session save handler
 */
class DbTable extends \SessionHandler
{
    /**
     * Session data table name
     *
     * @var string
     */
    protected $_sessionTable;

    /**
     * Database write connection
     *
     * @var \Magento\DB\Adapter\AdapterInterface
     */
    protected $_write;

    /**
     * Constructor
     *
     * @param \Magento\App\Resource $resource
     */
    public function __construct(\Magento\App\Resource $resource)
    {
        $this->_sessionTable = $resource->getTableName('core_session');
        $this->_write        = $resource->getConnection('core_write');
        $this->checkConnection();
    }

    /**
     * Check DB connection
     */
    protected function checkConnection()
    {
        if (!$this->_write) {
            throw new \Magento\Session\SaveHandlerException('Write DB connection is not available');
        }
        if (!$this->_write->isTableExists($this->_sessionTable)) {
            throw new \Magento\Session\SaveHandlerException('DB storage table does not exist');
        }
    }

    /**
     * Open session
     *
     * @param string $savePath ignored
     * @param string $sessionName ignored
     * @return boolean
     */
    public function open($savePath, $sessionName)
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
        // need to use write connection to get the most fresh DB sessions
        $select = $this->_write->select()
            ->from($this->_sessionTable, array('session_data'))
            ->where('session_id = :session_id');
        $bind = array('session_id' => $sessionId);
        $data = $this->_write->fetchOne($select, $bind);

        // check if session data is a base64 encoded string
        $decodedData = base64_decode($data, true);
        if ($decodedData !== false) {
            $data = $decodedData;
        }
        return $data;
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
        // need to use write connection to get the most fresh DB sessions
        $bindValues = array('session_id' => $sessionId);
        $select = $this->_write->select()
            ->from($this->_sessionTable)
            ->where('session_id = :session_id');
        $exists = $this->_write->fetchOne($select, $bindValues);

        // encode session serialized data to prevent insertion of incorrect symbols
        $sessionData = base64_encode($sessionData);
        $bind = array(
            'session_expires' => time(),
            'session_data'    => $sessionData,
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
     * @param string $sessionId
     * @return boolean
     */
    public function destroy($sessionId)
    {
        $where = array('session_id = ?' => $sessionId);
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
