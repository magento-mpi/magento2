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
     * Maintenance flag file name
     */
    const FLAG_FILENAME = '.maintenance.flag';

    /**
     * IP-addresses file name
     */
    const IP_FILENAME = '.maintenance.ip';

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
     * Gets detailed information about whether it is enabled and for which IP-addresses (if any)
     *
     * @return array|bool
     */
    public function getStatusInfo()
    {
        $file = $this->getFile(self::FLAG_FILENAME);
        if (file_exists($file)) {
            return $this->getAddressInfo();
        }
        return false;
    }

    /**
     * Get list of IP addresses effective for maintenance mode
     *
     * @return string[]
     */
    public function getAddressInfo()
    {
        $file = $this->getFile(self::IP_FILENAME);
        if (file_exists($file)) {
            return explode(',', trim(file_get_contents($file)));
        } else {
            return [];
        }
    }

    /**
     * Checks whether mode is on
     *
     * Optionally specify an IP-address to compare against the white list
     *
     * @param string $remoteAddr
     * @return bool
     */
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
     * @param array $addresses
     * @return void
     */
    public function turnOn(array $addresses = null)
    {
        $this->set(1, $addresses);
    }

    /**
     * Turn off store maintenance mode
     *
     * @param array $addresses
     * @return void
     */
    public function turnOff(array $addresses = null)
    {
        $this->set(0, $addresses);
    }

    /**
     * Subroutine to set the maintenance mode to the specified values
     *
     * @param bool|int $isSet
     * @param array|null $addresses
     */
    private function set($isSet, $addresses)
    {
        if (null === $addresses) {
            $addresses = $this->getAddressInfo();
        }
        $flagFile = $this->getFile(self::FLAG_FILENAME);
        if ($isSet) {
            touch($flagFile);
        } elseif (file_exists($flagFile)) {
            unlink($flagFile);
        }
        $addressesFile = $this->getFile(self::IP_FILENAME);
        if (empty($addresses)) {
            if (file_exists($addressesFile)) {
                unlink($addressesFile);
            }
        } else {
            file_put_contents($addressesFile, implode(',', $addresses));
        }
    }

    /**
     * Gets the absolute file name from the configured directory
     *
     * @param string $basename
     * @return string
     */
    private function getFile($basename)
    {
        return $this->dirList->getDir(self::FLAG_DIR) . '/' . $basename;
    }
}
