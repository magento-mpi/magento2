<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Setup\Model;

/**
 * UI Logger
 *
 * @package Magento\Setup\Model
 */
class WebLogger implements LoggerInterface
{
    /**
     * Log File
     *
     * @var string
     */
    protected $logFile = 'install.log';

    /**
     * @var resource
     */
    protected $resource;

    /**
     * @var bool
     */
    protected $hasError = false;

    public function __construct()
    {
        $this->logFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $this->logFile;
    }

    public function open($mode)
    {
        $this->resource = @fopen($this->logFile, $mode);
    }

    public function close()
    {
        fclose($this->resource);
    }

    /**
     * {@inheritdoc}
     */
    public function logSuccess($message)
    {
        $this->writeToFile('<span class="text-success">[SUCCESS] ' . $message . '</span>');
    }

    /**
     * {@inheritdoc}
     */
    public function logError(\Exception $e)
    {
        $this->writeToFile('<span class="text-danger">[ERROR] ' . $e . '<span>');
    }

    /**
     * {@inheritdoc}
     */
    public function log($message)
    {
        $this->writeToFile('<span class="text-info">' . $message . '</span>');
    }

    /**
     * Write the message to file
     *
     * @param string $message
     * @return void
     */
    private function writeToFile($message)
    {
        $this->open('a+');
        fwrite($this->resource, $message . PHP_EOL);
        $this->close();
    }

    /**
     * @return bool
     */
    public function hasError()
    {
        return $this->hasError;
    }

    /**
     * @return array
     */
    public function get()
    {
        $this->open('r+');
        fseek($this->resource, 0);
        $messages = [];
        while (($string = fgets($this->resource)) !== false) {
            if (strpos($string, '[ERROR]') !== false) {
                $this->hasError = true;
            }
            $messages[] = $string;
        }
        $this->close();
        return $messages;
    }

    public function clear()
    {
        @unlink($this->logFile);
    }
}
