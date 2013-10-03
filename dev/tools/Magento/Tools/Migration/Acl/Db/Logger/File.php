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
 * Db migration logger. Output result put to file
 */
namespace Magento\Tools\Migration\Acl\Db\Logger;

class File extends \Magento\Tools\Migration\Acl\Db\AbstractLogger
{
    /**
     * Path to log file
     *
     * @var string
     */
    protected $_file = null;

    public function __construct($file)
    {
        $logDir = realpath(__DIR__ . '/../../') . '/log/';
        if (false == is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        if (false == is_writeable($logDir)) {
            throw new \InvalidArgumentException('Directory ' . dirname($logDir) . ' is not writeable');
        }

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
        file_put_contents($this->_file, (string)$this);
    }
}

