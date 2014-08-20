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
     *
     * DO NOT consolidate this file and the IP white list into one.
     * It is going to work much faster in 99% of cases: the isOn() will return false whenever file doesn't exist.
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
     * Checks whether mode is on
     *
     * Optionally specify an IP-address to compare against the white list
     *
     * @param string $remoteAddr
     * @return bool
     */
    public function isOn($remoteAddr = '')
    {
        $file = $this->getFile(self::FLAG_FILENAME);
        if (!file_exists($file)) {
            return false;
        }
        $info = $this->getAddressInfo();
        return !in_array($remoteAddr, $info);
    }

    /**
     * Sets maintenance mode "on" or "off"
     *
     * @param bool $isOn
     * @return bool
     */
    public function set($isOn)
    {
        $flagFile = $this->getFile(self::FLAG_FILENAME);
        if ($isOn) {
            return touch($flagFile);
        }
        if (file_exists($flagFile)) {
            return unlink($flagFile);
        }
        return true;
    }

    /**
     * Sets list of allowed IP addresses
     *
     * @param string $addresses
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function setAddresses($addresses)
    {
        $addresses = (string)$addresses;
        $addressesFile = $this->getFile(self::IP_FILENAME);
        if (empty($addresses)) {
            if (file_exists($addressesFile)) {
                return unlink($addressesFile);
            }
            return true;
        }
        if (!preg_match('/^[^\s,]+(,[^\s,]+)*$/', $addresses)) {
            throw new \InvalidArgumentException("One or more IP-addresses is expected (comma-separated)\n");
        }
        $result = file_put_contents($addressesFile, $addresses);
        return false !== $result ? true : false;
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
