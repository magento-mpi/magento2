<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App;

use Magento\Framework\App\Filesystem;
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
     * Path to store files
     * @var \Magento\Framework\Filesystem\Directory\Write
     */
    protected $flagDir;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->flagDir = $filesystem->getDirectoryWrite(self::FLAG_DIR);
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
        if (!$this->flagDir->getDriver()->isExists($file)) {
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
        if ($isOn) {
            return $this->flagDir->touch(self::FLAG_FILENAME);
        }
        if ($this->flagDir->getDriver()->isExists($this->getFile(self::FLAG_FILENAME))) {
            return $this->flagDir->delete(self::FLAG_FILENAME);
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
        if (empty($addresses)) {
            if ($this->flagDir->getDriver()->isExists($this->getFile(self::IP_FILENAME))) {
                return $this->flagDir->delete(self::IP_FILENAME);
            }
            return true;
        }
        if (!preg_match('/^[^\s,]+(,[^\s,]+)*$/', $addresses)) {
            throw new \InvalidArgumentException("One or more IP-addresses is expected (comma-separated)\n");
        }
        $result = $this->flagDir->writeFile(self::IP_FILENAME, $addresses);
        return false !== $result ? true : false;
    }

    /**
     * Get list of IP addresses effective for maintenance mode
     *
     * @return string[]
     */
    public function getAddressInfo()
    {
        if ($this->flagDir->getDriver()->isExists($this->getFile(self::IP_FILENAME))) {
            $temp = $this->flagDir->readFile(self::IP_FILENAME);
            return explode(',', trim($temp));
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
        return $this->flagDir->getAbsolutePath() . $basename;
    }
}
