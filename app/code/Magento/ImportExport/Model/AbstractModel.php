<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Operation abstract class
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\ImportExport\Model;

abstract class AbstractModel extends \Magento\Object
{
    /**
     * Enable loging
     *
     * @var boolean
     */
    protected $_debugMode = false;

    /**
     * Loger instance
     * @var \Magento\Core\Model\Log\Adapter
     */
    protected $_logInstance;

    /**
     * Fields that should be replaced in debug with '***'
     *
     * @var array
     */
    protected $_debugReplacePrivateDataKeys = array();

    /**
     * Contains all log information
     *
     * @var array
     */
    protected $_logTrace = array();

    /**
     * @var \Magento\Logger
     */
    protected $_logger;

    /**
     * @var \Magento\App\Dir
     */
    protected $_dir;

    /**
     * @var \Magento\Core\Model\Log\AdapterFactory
     */
    protected $_adapterFactory;

    /**
     * @param \Magento\Logger $logger
     * @param \Magento\App\Dir $dir
     * @param \Magento\Core\Model\Log\AdapterFactory $adapterFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Logger $logger,
        \Magento\App\Dir $dir,
        \Magento\Core\Model\Log\AdapterFactory $adapterFactory,
        array $data = array()
    ) {
        $this->_logger = $logger;
        $this->_dir = $dir;
        $this->_adapterFactory = $adapterFactory;
        parent::__construct($data);
    }

    /**
     * Log debug data to file.
     * Log file dir: var/log/import_export/%Y/%m/%d/%time%_%operation_type%_%entity_type%.log
     *
     * @param mixed $debugData
     * @return \Magento\ImportExport\Model\AbstractModel
     */
    public function addLogComment($debugData)
    {
        if (is_array($debugData)) {
            $this->_logTrace = array_merge($this->_logTrace, $debugData);
        } else {
            $this->_logTrace[] = $debugData;
        }
        if (!$this->_debugMode) {
            return $this;
        }

        if (!$this->_logInstance) {
            $dirName  = date('Y/m/d/');
            $fileName = join('_', array(
                str_replace(':', '-', $this->getRunAt()),
                $this->getScheduledOperationId(),
                $this->getOperationType(),
                $this->getEntity()
            ));
            $dirPath = $this->_dir->getDir('var') . '/' . \Magento\ImportExport\Model\Scheduled\Operation::LOG_DIRECTORY
                . $dirName;
            if (!is_dir($dirPath)) {
                mkdir($dirPath, 0777, true);
            }
            $fileName = substr(strstr(\Magento\ImportExport\Model\Scheduled\Operation::LOG_DIRECTORY, '/'), 1)
                . $dirName . $fileName . '.log';
            $this->_logInstance = $this->_adapterFactory->create(array('fileName' => $fileName))
                ->setFilterDataKeys($this->_debugReplacePrivateDataKeys);
        }
        $this->_logInstance->log($debugData);
        return $this;
    }

    /**
     * Return human readable debug trace.
     *
     * @return array
     */
    public function getFormatedLogTrace()
    {
        $trace = '';
        $lineNumber = 1;
        foreach ($this->_logTrace as &$info) {
            $trace .= $lineNumber++ . ': ' . $info . "\n";
        }
        return $trace;
    }
}
