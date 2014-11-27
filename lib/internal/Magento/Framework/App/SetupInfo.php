<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\App;

/**
 * A model for determining information about setup application
 */
class SetupInfo
{
    /**#@+
     * Initialization parameters for redirecting if the application is not installed
     */
    const NOT_INSTALLED_URL_PATH_PARAM = 'MAGE_NOT_INSTALLED_URL_PATH';
    const NOT_INSTALLED_URL_PARAM = 'MAGE_NOT_INSTALLED_URL';
    /**#@-*/

    /**
     * Default path relative to the project root
     */
    const DEFAULT_PATH = 'setup';

    /**
     * Environment variables
     *
     * @var array
     */
    private $server;

    /**
     * Constructor
     *
     * @param array $server
     */
    public function __construct($server)
    {
        $this->server = $server;
    }

    /**
     * Gets setup application URL
     *
     * @return string
     */
    public function getUrl()
    {
        if (isset($this->server[self::NOT_INSTALLED_URL_PARAM])) {
            return $this->server[self::NOT_INSTALLED_URL_PARAM];
        }
        return \Magento\Framework\App\Request\Http::getDistroBaseUrlPath($this->server) . trim($this->getPath(), '/') . '/';
    }

    /**
     * Gets setup application directory path in the filesystem
     *
     * @param string $projectRoot
     * @return string
     */
    public function getDir($projectRoot)
    {
        return rtrim($projectRoot, '/') . '/' . trim($this->getPath(), '/');
    }

    /**
     * Checks if the setup application is available in current document root
     *
     * @param string $projectRoot
     * @return bool
     */
    public function isAvailable($projectRoot)
    {
        if (isset($this->server['DOCUMENT_ROOT'])) {
            $docRoot = str_replace('\\', '/', realpath($this->server['DOCUMENT_ROOT']));
            $installDir = str_replace('\\', '/', realpath($this->getDir($projectRoot)));
            return false !== strpos($installDir . '/', $docRoot . '/');
        }
        return false;
    }

    /**
     * Gets relative path to setup application
     *
     * @return string
     */
    private function getPath()
    {
        if (isset($this->server[self::NOT_INSTALLED_URL_PATH_PARAM])) {
            return $this->server[self::NOT_INSTALLED_URL_PATH_PARAM];
        }
        return self::DEFAULT_PATH;
    }
}
