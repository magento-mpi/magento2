<?php
/**
 * Application file system directories dictionary
 *
 * Provides information about what directories are available in the application
 * Serves as customizaiton point to specify different directories or add own
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Filesystem;

use Magento\Config\ConfigFactory;

class DirectoryList
{
    /**
     * @var ConfigFactory
     */
    protected $configFactory;

    /**
     * @var \Magento\Config\Config
     */
    protected $config;

    /**
     * @var string
     */
    protected $root;

    /**
     * @param ConfigFactory $configFactory
     */
    public function __construct(ConfigFactory $configFactory)
    {
        $this->configFactory = $configFactory;
        $this->config = $this->configFactory->create();

        $this->root = str_replace('\\', '/', $this->config->getMagentoBasePath());

        foreach ($this->config->getMagentoFilePermissions() as $code => $config) {
            if (!$this->isAbsolute($config['path'])) {
                $this->directories[$code]['path'] = $this->makeAbsolute($config['path']);
            }
        }
    }

    /**
     * Add root dir for relative path
     *
     * @param string $path
     * @return string
     */
    protected function makeAbsolute($path)
    {
        if ($path === null) {
            $result = '';
        } else {
            $result = $this->root;
            if (!empty($path)) {
                $result .= '/' . $path;
            }
        }
        return $result;
    }

    /**
     * Verify if path is absolute
     *
     * @param string $path
     * @return bool
     */
    protected function isAbsolute($path)
    {
        $path = strtr($path, '\\', '/');
        $isUnixRoot = strpos($path, '/') === 0;
        $isWindowsRoot = preg_match('#^\w{1}:/#', $path);
        $isWindowsLetter = parse_url($path, PHP_URL_SCHEME) !== null;

        return $isUnixRoot || $isWindowsRoot || $isWindowsLetter;
    }

    /**
     * Get configuration for directory code
     *
     * @param string $code
     * @return array
     * @throws \Magento\Framework\Filesystem\FilesystemException
     */
    public function getConfig($code)
    {
        if (!isset($this->directories[$code])) {
            throw new \Magento\Framework\Filesystem\FilesystemException(
                sprintf('The "%s" directory is not specified in configuration', $code)
            );
        }
        return $this->directories[$code];
    }
}
