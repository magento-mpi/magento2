<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Migration logger. Output result put to file
 */
class Magento_Tools_Migration_System_Configuration_Logger_File
    extends Magento_Tools_Migration_System_Configuration_LoggerAbstract
{
    /**
     * Path to log file
     *
     * @var string
     */
    protected $_file = null;

    /**
     * @var Magento_Tools_Migration_System_FileManager
     */
    protected $_fileManager;

    /**
     * @param string $file
     * @param Magento_Tools_Migration_System_FileManager $fileManger
     * @throws InvalidArgumentException
     */
    public function __construct($file, Magento_Tools_Migration_System_FileManager $fileManger)
    {
        $this->_fileManager = $fileManger;

        $logDir = realpath(__DIR__ . '/../../') . '/log/';

        if (empty($file)) {
            throw new InvalidArgumentException('Log file name is required');
        }
        $this->_file = $logDir . $file;
    }

    /**
     * Put report to file
     */
    public function report()
    {
        $this->_fileManager->write($this->_file, (string)$this);
    }
}
