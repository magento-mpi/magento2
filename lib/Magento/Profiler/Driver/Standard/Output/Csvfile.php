<?php
/**
 * Class that represents profiler output in CSV-file format
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Profiler_Driver_Standard_Output_Csvfile extends Magento_Profiler_Driver_Standard_OutputAbstract
{
    const DEFAULT_FILEPATH = '/var/log/profiler.csv';

    /**
     *
     * @var string
     */
    protected $_filePath;

    /**
     * @var string
     */
    protected $_delimiter;

    /**
     * @var string
     */
    protected $_enclosure;

    /**
     * Constructor
     *
     * @param array|null $config
     */
    public function __construct(array $config = null)
    {
        parent::__construct($config);
        $this->_filePath = $this->_parseFilePath($config);
        $this->_delimiter = isset($config['delimiter']) ? $config['delimiter'] : ',';
        $this->_enclosure = isset($config['enclosure']) ? $config['enclosure'] : '"';
    }

    /**
     * Parses file path
     *
     * @param array|null $config
     * @return string
     */
    protected function _parseFilePath(array $config = null)
    {
        $result = isset($config['filePath']) ? $config['filePath'] : self::DEFAULT_FILEPATH;

        if (isset($config['baseDir'])) {
            $result = rtrim($config['baseDir'], DIRECTORY_SEPARATOR)
                . DIRECTORY_SEPARATOR
                . ltrim($result, DIRECTORY_SEPARATOR);
        }
        return $result;
    }

    /**
     * Write profiling results to CSV-file
     *
     * @param Magento_Profiler_Driver_Standard_Stat $stat
     * @throws RuntimeException if output file cannot be opened
     */
    public function display(Magento_Profiler_Driver_Standard_Stat $stat)
    {
        $fileHandle = fopen($this->_filePath, 'w');
        if (!$fileHandle) {
            throw new RuntimeException(sprintf('Can not open a file "%s".', $this->_filePath));
        }

        $lockRequired = (strpos($this->_filePath, 'php://') !== 0);
        $isLocked = false;
        while ($lockRequired && !$isLocked) {
            $isLocked = flock($fileHandle, LOCK_EX);
        }
        $this->_writeFileContent($fileHandle, $stat);
        if ($isLocked) {
            flock($fileHandle, LOCK_UN);
        }
        fclose($fileHandle);
    }

    /**
     * Write content into an opened file handle
     *
     * @param resource $fileHandle
     * @param Magento_Profiler_Driver_Standard_Stat $stat
     */
    protected function _writeFileContent($fileHandle, Magento_Profiler_Driver_Standard_Stat $stat)
    {
        foreach ($this->_getTimerIds($stat) as $timerName) {
            $row = array();
            foreach ($this->_columns as $column) {
                $row[] = $this->_renderColumnValue($stat->fetch($timerName, $column), $column);
            }
            fputcsv($fileHandle, $row, $this->_delimiter, $this->_enclosure);
        }
    }
}
