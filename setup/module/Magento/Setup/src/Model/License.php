<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Setup\Model;

use Magento\Config\Config;
use Magento\Config\ConfigFactory;

/**
 * License model
 *
 * @package Magento\Setup\Model
 */
class License
{
    /**
     * License File location
     *
     * @var string
     */
    const LICENSE_FILENAME = 'LICENSE.txt';

    /**
     * Path of license file
     *
     * @var string
     */
    protected $licenseFile;

    /**
     * Configuration details
     *
     * @var Config
     */
    protected $config;

    /**
     * ConfigFactory to create config
     *
     * @var ConfigFactory
     */
    protected $configFactory;

    /**
     * Constructor
     *
     * @param ConfigFactory $configFactory
     */
    public function __construct(ConfigFactory $configFactory)
    {
        $this->configFactory = $configFactory;
        $this->config = $this->configFactory->create();
        $this->licenseFile = $this->config->getMagentoBasePath() . DIRECTORY_SEPARATOR . self::LICENSE_FILENAME;
    }

    /**
     * Checks if license file exists
     *
     * @return boolean
     */
    public function checkLicenseFile(){

        if(file_exists($this->licenseFile)) {
            return true;
        }
        return false;
    }

    /**
     * Returns contents of License file.
     *
     * @return string
     */
    public function getContents()
    {
        $resource = @fopen($this->licenseFile, "r+");
        $contents = nl2br(fread($resource, filesize($this->licenseFile)));
        fclose($resource);
        return $contents;
    }
}
