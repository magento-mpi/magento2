<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Setup\Model;

class Logger
{
    /**
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

    public function open()
    {
        $this->resource = @fopen($this->logFile, 'a+');
    }

    public function close()
    {
        fclose($this->resource);
    }

    /**
     * @param string $moduleName
     */
    public function logSuccess($moduleName)
    {
        $this->open();
        fwrite($this->resource, '<span class="text-success">[SUCCESS] ' . $moduleName . ' ... installed</span>' . PHP_EOL);
        $this->close();
    }

    /**
     * @param \Exception $e
     */
    public function logError(\Exception $e)
    {
        $this->open();
        fwrite($this->resource, '<span class="text-danger">[ERROR] ' . $e . '<span>' . PHP_EOL);
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
        $this->open();
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
 