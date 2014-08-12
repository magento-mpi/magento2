<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Application Maintenance Mode
 */
class MaintenanceMode
{
    /**
     * Maintenance flag name
     */
    const FLAG_FILENAME = '.maintenance';

    /**
     * Maintenance flag dir
     */
    const FLAG_DIR = Filesystem::VAR_DIR;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $dirList;

    /**
     * @param \Magento\Framework\App\Filesystem\DirectoryList $dirList
     */
    public function __construct(DirectoryList $dirList)
    {
        $this->dirList = $dirList;
    }

    /**
     * @return array|bool
     */
    public function getStatusInfo()
    {
        $file = $this->getFile();
        if (file_exists($file)) {
            return explode(",", file_get_contents($file));
        }
        return false;
    }

    public function isOn($remoteAddr = '')
    {
        $info = $this->getStatusInfo();
        if (is_array($info)) {
            return !in_array($remoteAddr, $info);
        }
        return false;
    }

    /**
     * Turn on store maintenance mode
     *
     * @param string $data
     * @return int|bool
     */
    public function turnOn($data = 'maintenance')
    {
        $file = $this->getFile();
        return file_put_contents($file, $data);
    }

    /**
     * Turn off store maintenance mode
     *
     * @return bool
     */
    public function turnOff()
    {
        $file = $this->getFile();
        if (!file_exists($file)) {
            return true;
        }
        return unlink($file);
    }

    private function getFile()
    {
        return $this->dirList->getDir(self::FLAG_DIR) . '/' . self::FLAG_FILENAME;
    }
}
