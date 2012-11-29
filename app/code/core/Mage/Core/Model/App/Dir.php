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
     * Key-values of registered directories
     *
     * Format: array(<code> => <relative_path>)
     *
     * @var array
     */
    private $_dirs = array(
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
        foreach ($customDirs as $code => $relative) {
            $this->_set($code, $relative);
        }
        foreach ($this->_dirs as $code => $relative) {
            if (isset($customPaths[$code])) {
                $this->_setPath($code, $customPaths[$code]);
            } else {
                $this->_setPath($code, $basePath . ($relative ? DIRECTORY_SEPARATOR . $relative : ''));
            }
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
        if (!preg_match('/^([a-z0-9]+[a-z0-9\.]*(\/[a-z0-9]+[a-z0-9\.]*)*)?$/', $relativePath)) {
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
