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
class Mage_Core_Model_App_Dir
{
    /**#@+
     * Dictionary of available directory codes
     */
    const ROOT    = 'base';
    const APP     = 'app';
    const CODE    = 'code';
    const VIEW    = 'design';
    const CONFIG  = 'etc';
    const LIB     = 'lib';
    const LOCALE  = 'locale';
    const PUB     = 'pub';
    const PUB_LIB = 'pub_lib'; // 'js'
    const MEDIA   = 'media';
    const VAR_DIR = 'var';
    const TMP     = 'tmp';
    const CACHE   = 'cache';
    const LOG     = 'log';
    const SESSION = 'session';
    const UPLOAD  = 'upload';
    const EXPORT  = 'export';
    /**#@-*/

    /**
     * Default values for directories
     *
     * Format: array(<code> => <relative_path>)
     *
     * @var array
     */
    private static $_defaults = array(
        self::ROOT    => '',
        self::APP     => 'app',
        self::CODE    => 'app/code',
        self::VIEW    => 'app/design',
        self::CONFIG  => 'app/etc',
        self::LIB     => 'lib',
        self::LOCALE  => 'app/locale',
        self::PUB     => 'pub',
        self::PUB_LIB => 'pub/lib',
        self::MEDIA   => 'pub/media',
        self::VAR_DIR => 'var',
        self::TMP     => 'var/tmp',
        self::CACHE   => 'var/cache',
        self::LOG     => 'var/log',
        self::SESSION => 'var/session',
        self::UPLOAD  => 'pub/media/upload',
        self::EXPORT  => 'var/export',
    );

    /**
     * @var array
     */
    private $_dirs = array();

    /**
     * @var array
     */
    private $_paths = array();

    /**
     * List of directories that application requires to be writable in order to operate
     *
     * @return array
     */
    public static function getWritableDirs()
    {
        return array(self::MEDIA, self::VAR_DIR, self::TMP, self::CACHE, self::LOG, self::SESSION, self::EXPORT);
    }

    /**
     * Verify the base directory and initialize custom paths
     *
     * @param string $basePath
     * @param array $customDirs
     * @param array $customPaths
     */
    public function __construct($basePath, array $customDirs = array(), array $customPaths = array())
    {
        $this->_dirs = self::$_defaults;
        foreach ($customDirs as $code => $relative) {
            $this->_set($code, $relative);
        }
        foreach (array_keys($this->_dirs) as $code) {
            if (isset($customPaths[$code])) {
                $path = $customPaths[$code];
            } else {
                $path = $this->_getDefaultPath($basePath, $code);
            }
            $this->_setPath($code, $path);
        }
    }

    /**
     * Get a relative path from directories registry
     *
     * @param string $code
     * @return string|bool
     */
    public function get($code)
    {
        return isset($this->_dirs[$code]) ? $this->_dirs[$code] : false;
    }

    /**
     * Set a relative path to directories registry
     *
     * The method is private on purpose: it must be used only in constructor. Users of this object must not be able
     * to alter its state, otherwise it may compromise application integrity.
     * Path must be usable as a fragment of a URL path.
     * For interoperability and security purposes, no uppercase or "upper directory" paths like "." or ".."
     *
     * @param $code
     * @param $relativePath
     * @throws InvalidArgumentException
     */
    private function _set($code, $relativePath)
    {
        if (!preg_match('/^([a-z0-9_]+[a-z0-9\._]*(\/[a-z0-9_]+[a-z0-9\._]*)*)?$/', $relativePath)) {
            throw new InvalidArgumentException(
                "Must be relative directory path in lowercase with '/' directory separator: '{$relativePath}'"
            );
        }
        $this->_dirs[$code] = $relativePath;
    }

    /**
     * Compose an absolute path from directories registry
     *
     * @param string $code
     * @return string|bool
     */
    public function getPath($code)
    {
        return isset($this->_paths[$code]) ? $this->_paths[$code] : false;
    }

    /**
     * Build a path to directory based on default values
     *
     * @param string $basePath
     * @param string $code
     * @return string
     */
    private function _getDefaultPath($basePath, $code)
    {
        $parentCode = $this->_getDefaultParent($code);
        if ($parentCode) {
            $path = $this->_paths[$parentCode];
            $relative = substr(self::$_defaults[$code], strlen(self::$_defaults[$parentCode]));
            $relative = ltrim($relative, '/');
        } else {
            $path = $basePath;
            $relative = $this->_dirs[$code];
        }
        return $path . ($relative ? DIRECTORY_SEPARATOR . $relative : '');
    }

    /**
     * Get code of an item defined as parent by default for the specified code
     *
     * @param $code
     * @return string|bool
     */
    private static function _getDefaultParent($code)
    {
        switch ($code) {
            case self::CODE:
            case self::VIEW:
            case self::CONFIG:
            case self::LOCALE:
                return self::APP;
            case self::PUB_LIB:
            case self::MEDIA:
                return self::PUB;
            case self::UPLOAD:
                return self::MEDIA;
            case self::TMP:
            case self::CACHE:
            case self::LOG:
            case self::SESSION:
            case self::EXPORT:
                return self::VAR_DIR;
            default:
                return false;
        }
    }

    /**
     * Set an absolute path to directories registry
     *
     * Similar to _set() method, this is just a sub-routine of constructor
     *
     * @param string $code
     * @param string $path
     */
    private function _setPath($code, $path)
    {
        $this->_paths[$code] = str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $path);
    }
}
