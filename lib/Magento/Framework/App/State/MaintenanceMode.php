<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\State;

use Magento\Framework\App\Filesystem;

/**
 * Application Maintenance Mode
 */
class MaintenanceMode
{
    /**
     * Maintenance flag name
     */
    const FLAG_FILENAME = 'maintenance.flag';

    /**
     * Maintenance flag dir
     */
    const FLAG_DIR = Filesystem::VAR_DIR;

    /**
     * @var \Magento\Framework\App\Filesystem
     */
    protected $filesystem;

    /**
     * @param \Magento\Framework\App\Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Turn on store maintenance mode
     *
     * @param string $data
     * @return bool
     */
    public function turnOn($data = 'maintenance')
    {
        try {
            $this->filesystem->getDirectoryWrite(self::FLAG_DIR)
                ->writeFile(self::FLAG_FILENAME, $data);
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Turn off store maintenance mode
     *
     * @return bool
     */
    public function turnOff()
    {
        try {
            $this->filesystem->getDirectoryWrite(self::FLAG_DIR)->delete(self::FLAG_FILENAME);
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }
}
