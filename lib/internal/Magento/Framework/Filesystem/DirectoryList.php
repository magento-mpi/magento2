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
namespace Magento\Framework\Filesystem;

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
     * Constructor
     *
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
     * Gets path of a directory
     *
     * @param string $code
     * @return string
     */
    public function getPath($code)
    {
        $this->assertCode($code);
        return $this->directories[$code]['path'];
    }

    /**
     * Gets URL path of a directory
     *
     * @param string $code
     * @return string
     */
    public function getUrlPath($code)
    {
        $this->assertCode($code);
        return $this->directories[$code]['uri'];
    }

    /**
     * Assert that specified directory code is in the registry
     *
     * @param string $code
     * @throws FilesystemException
     * @return void
     */
    private function assertCode($code)
    {
        if (!isset($this->directories[$code])) {
            throw new FilesystemException("Unknown directory type: '$code'");
        }
    }

    /**
     * Set URI
     *
     * The method is private on purpose: it must be used only in constructor. Users of this object must not be able
     * to alter its state, otherwise it may compromise application integrity.
     * Path must be usable as a fragment of a URL path.
     * For interoperability and security purposes, no uppercase or "upper directory" paths like "." or ".."
     *
     * @param string $code
     * @param string $uri
     * @return void
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
     * @return void
     */
    private function setPath($code, $path)
    {
        $this->directories[$code]['path'] = str_replace('\\', '/', $path);
    }
}
