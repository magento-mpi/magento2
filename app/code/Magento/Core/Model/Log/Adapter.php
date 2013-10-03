<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Log Adapter
 *
 * @category   Magento
 * @package    Magento_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Model\Log;

class Adapter
{

    /**
     * Store log file name
     *
     * @var string
     */
    protected $_logFileName = '';

    /**
     * Data to log
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Fields that should be replaced in debug data with '***'
     *
     * @var array
     */
    protected $_debugReplacePrivateDataKeys = array();

    /**
     * @var \Magento\Core\Model\Logger
     */
    protected $_logger;

    /**
     * Set log file name
     *
     * @param \Magento\Core\Model\Logger $logger
     * @param string $fileName
     */
    public function __construct(\Magento\Core\Model\Logger $logger, $fileName)
    {
        $this->_logFileName = $fileName;
        $this->_logger = $logger;
    }

    /**
     * Perform forced log data to file
     *
     * @param mixed $data
     * @return \Magento\Core\Model\Log\Adapter
     */
    public function log($data = null)
    {
        if ($data === null) {
            $data = $this->_data;
        }
        else {
            if (!is_array($data)) {
                $data = array($data);
            }
        }
        $data = $this->_filterDebugData($data);
        $data['__pid'] = getmypid();
        $this->_logger->logFile($data, \Zend_Log::DEBUG, $this->_logFileName);
        return $this;
    }

    /**
     * Log data setter
     *
     * @param string|array $key
     * @param mixed $value
     * @return \Magento\Core\Model\Log\Adapter
     * @todo replace whole data
     */
    public function setData($key, $value = null)
    {
        if(is_array($key)) {
            $this->_data = $key;
        }
        else {
            $this->_data[$key] = $value;
        }
        return $this;
    }

    /**
     * Setter for private data keys, that should be replaced in debug data with '***'
     *
     * @param array $keys
     * @return \Magento\Core\Model\Log\Adapter
     */
    public function setFilterDataKeys($keys)
    {
        if (!is_array($keys)) {
            $keys = array($keys);
        }
        $this->_debugReplacePrivateDataKeys = $keys;
        return $this;
    }

    /**
     * Recursive filter data by private conventions
     *
     * @param mixed $debugData
     * @return mixed
     */
    protected function _filterDebugData($debugData)
    {
        if (is_array($debugData) && is_array($this->_debugReplacePrivateDataKeys)) {
            foreach ($debugData as $key => $value) {
                if (in_array($key, $this->_debugReplacePrivateDataKeys)) {
                    $debugData[$key] = '****';
                }
                else {
                    if (is_array($debugData[$key])) {
                        $debugData[$key] = $this->_filterDebugData($debugData[$key]);
                    }
                }
            }
        }
        return $debugData;
    }
}
