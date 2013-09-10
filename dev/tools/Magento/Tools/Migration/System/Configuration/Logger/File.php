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
namespace Magento\Tools\Migration\System\Configuration\Logger;

class File
    extends \Magento\Tools\Migration\System\Configuration\LoggerAbstract
{
    /**
     * Path to log file
     *
     * @var string
     */
    protected $_file = null;

    /**
     * @var \Magento\Tools\Migration\System\FileManager
     */
    protected $_fileManager;

    /**
     * @param string $file
     * @param \Magento\Tools\Migration\System\FileManager $fileManger
     * @throws \InvalidArgumentException
     */
    public function __construct($file, \Magento\Tools\Migration\System\FileManager $fileManger)
    {
        $this->_fileManager = $fileManger;

        $logDir = realpath(__DIR__ . '/../../') . '/log/';

        if (empty($file)) {
            throw new \InvalidArgumentException('Log file name is required');
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
