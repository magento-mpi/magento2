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

use Magento\Filesystem;

class DirectoryList
{
    /**
     * Root path
     *
     * @var string
     */
    protected $root;

    /**
     * Directories configurations
     *
     * @var array
     */
    protected $directories = array();

    /**
     * @var array
     */
    protected $protocol = array();

    /**
     * @param string $root
     * @param array $directories
     */
    public function __construct($root, array $directories = array())
    {
        $this->root = str_replace('\\', '/', $root);

        foreach ($this->directories as $code => $directoryConfig) {
            if (!$this->isAbsolute($directoryConfig['path'])) {
                $this->directories[$code]['path'] = $this->makeAbsolute($directoryConfig['path']);
            }
        }

        foreach ($directories as $code => $directoryConfig) {
            $baseConfiguration = isset($this->directories[$code]) ? $this->directories[$code] : array();
            $this->directories[$code] = array_merge($baseConfiguration, $directoryConfig);

            if (isset($directoryConfig['path'])) {
                $this->setPath($code, $directoryConfig['path']);
            }
            if (isset($directoryConfig['uri'])) {
                $this->setUri($code, $directoryConfig['uri']);
            }
        }
    }

    /**
     * Add directory configuration
     *
     * @param string $code
     * @param array $directoryConfig
     * @throws \Magento\Filesystem\FilesystemException
     */
    public function addDirectory($code, array $directoryConfig)
    {
        if (isset($this->directories[$code])) {
            throw new \Magento\Filesystem\FilesystemException("Configuration for '{$code}' already defined");
        }
        $this->setDirectory($code, $directoryConfig);
    }

    /**
     * @param string $code
     * @param array $directoryConfig
     */
    protected function setDirectory($code, array $directoryConfig)
    {
        if (!isset($directoryConfig['path'])) {
            $directoryConfig['path'] = null;
        }
        if (!$this->isAbsolute($directoryConfig['path'])) {
            $directoryConfig['path'] = $this->makeAbsolute($directoryConfig['path']);
        }

        $this->directories[$code] = $directoryConfig;
    }

    /**
     * Set protocol wrapper
     *
     * @param string $wrapperCode
     * @param array $configuration
     */
    public function addProtocol($wrapperCode, array $configuration)
    {
        $wrapperCode = isset($configuration['protocol']) ? $configuration['protocol'] : $wrapperCode;
        if (isset($configuration['wrapper'])) {
            $flag = isset($configuration['url_stream']) ? $configuration['url_stream'] : 0;
            $wrapperClass = $configuration['wrapper'];
            stream_wrapper_register($wrapperCode, $wrapperClass, $flag);
        }

        $this->protocol[$wrapperCode] = $configuration;
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
            $result = $this->getRoot();
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
     * Retrieve root path
     *
     * @return string
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Get configuration for directory code
     *
     * @param string $code
     * @return array
     * @throws \Magento\Filesystem\FilesystemException
     */
    public function getConfig($code)
    {
        if (!isset($this->directories[$code])) {
            throw new \Magento\Filesystem\FilesystemException(
                sprintf('The "%s" directory is not specified in configuration', $code)
            );
        }
        return $this->directories[$code];
    }

    /**
     * Return protocol configuration
     *
     * @param string $wrapperCode
     * @return null|array
     */
    public function getProtocolConfig($wrapperCode)
    {
        return isset($this->protocol[$wrapperCode]) ? $this->protocol[$wrapperCode] : null;
    }

    /**
     * \Directory path getter
     *
     * @param string $code One of self const
     * @return string|bool
     */
    public function getDir($code)
    {
        return isset($this->directories[$code]['path']) ? $this->directories[$code]['path'] : false;
    }

    /**
     * Set URI
     *
     * The method is private on purpose: it must be used only in constructor. Users of this object must not be able
     * to alter its state, otherwise it may compromise application integrity.
     * Path must be usable as a fragment of a URL path.
     * For interoperability and security purposes, no uppercase or "upper directory" paths like "." or ".."
     *
     * @param $code
     * @param $uri
     * @throws \InvalidArgumentException
     */
    private function setUri($code, $uri)
    {
        if (!preg_match('/^([a-z0-9_]+[a-z0-9\._]*(\/[a-z0-9_]+[a-z0-9\._]*)*)?$/', $uri)) {
            throw new \InvalidArgumentException(
                "Must be relative directory path in lowercase with '/' directory separator: '{$uri}'"
            );
        }
        $this->directories[$code]['uri'] = $uri;
    }

    /**
     * Set directory
     *
     * @param string $code
     * @param string $path
     */
    private function setPath($code, $path)
    {
        $this->directories[$code]['path'] = str_replace('\\', '/', $path);
    }
}
