<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ImportExport\Model;

/**
 * Operation abstract class
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class AbstractModel extends \Magento\Object
{
    /**
     * Enable loging
     *
     * @var bool
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
     * @var string[]
     */
    protected $_debugReplacePrivateDataKeys = array();

    /**
     * Contains all log information
     *
     * @var string[]
     */
    protected $_logTrace = array();

    /**
     * @var \Magento\Logger
     */
    protected $_logger;

    /**
     * @var \Magento\Filesystem\Directory\WriteInterface
     */
    protected $_varDirectory;

    /**
     * @var \Magento\Core\Model\Log\AdapterFactory
     */
    protected $_adapterFactory;

    /**
     * @param \Magento\Logger $logger
     * @param \Magento\App\Filesystem $filesystem
     * @param \Magento\Core\Model\Log\AdapterFactory $adapterFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Logger $logger,
        \Magento\App\Filesystem $filesystem,
        \Magento\Core\Model\Log\AdapterFactory $adapterFactory,
        array $data = array()
    ) {
        $this->_logger = $logger;
        $this->_varDirectory = $filesystem->getDirectoryWrite(\Magento\App\Filesystem::VAR_DIR);
        $this->_adapterFactory = $adapterFactory;
        parent::__construct($data);
    }

    /**
     * Log debug data to file.
     * Log file dir: var/log/import_export/%Y/%m/%d/%time%_%operation_type%_%entity_type%.log
     *
     * @param mixed $debugData
     * @return $this
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
            $path = 'import_export/'. $dirName;
            $this->_varDirectory->create($path);

            $fileName = $path . $fileName . '.log';
            $this->_logInstance = $this->_adapterFactory
                ->create(array('fileName' => $this->_varDirectory->getAbsolutePath($fileName)))
                ->setFilterDataKeys($this->_debugReplacePrivateDataKeys);
        }
        $this->_logInstance->log($debugData);
        return $this;
    }

    /**
     * Return human readable debug trace.
     *
     * @return string
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
