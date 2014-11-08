<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\DB\Logger;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

/**
 * Logging to file
 */
class File extends LoggerAbstract
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * Path to SQL debug data log
     *
     * @var string
     */
    protected $debugFile;

    /**
     * @param Filesystem $filesystem
     * @param string $debugFile
     * @param bool $logAllQueries
     * @param float $logQueryTime
     * @param bool $logCallStack
     */
    public function __construct(
        Filesystem $filesystem,
        $debugFile = 'var/debug/pdo_mysql.log',
        $logAllQueries = false,
        $logQueryTime = 0.05,
        $logCallStack = false
    ) {
        parent::__construct($logAllQueries, $logQueryTime, $logCallStack);
        $this->filesystem = $filesystem;
        $this->debugFile = $debugFile;
    }

    /**
     * {@inheritdoc}
     */
    public function log($str)
    {
        $str = '## ' . date('Y-m-d H:i:s') . "\r\n" . $str;

        $stream = $this->filesystem->getDirectoryWrite(DirectoryList::ROOT)->openFile($this->debugFile, 'a');
        $stream->lock();
        $stream->write($str);
        $stream->unlock();
        $stream->close();
    }

    /**
     * {@inheritdoc}
     */
    public function logStats($type, $sql, $bind = [], $result = null)
    {
        $this->log($this->getStats($type, $sql, $bind, $result));
    }

    /**
     * {@inheritdoc}
     */
    public function logException(\Exception $e)
    {
        $this->log($this->getExceptionMessage($e));
    }
}
