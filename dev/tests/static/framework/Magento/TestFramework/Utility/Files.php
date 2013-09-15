<?php
/**
 * A helper to gather specific kinds if files in Magento application
 *
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_TestFramework_Utility_Files
{
    /**
     * @var Magento_TestFramework_Utility_Files
     */
    protected static $_instance = null;

    /**
     * In-memory cache for the data sets
     *
     * @var array
     */
    protected static $_cache = array();

    /**
     * @var string
     */
    protected $_path = '';

    /**
     * Setter/Getter for an instance of self
     *
     * @param Magento_TestFramework_Utility_Files $instance
     * @return Magento_TestFramework_Utility_Files
     * @throws Exception when there is no instance set
     */
    public static function init(Magento_TestFramework_Utility_Files $instance = null)
    {
        if ($instance) {
            self::$_instance = $instance;
        }
        if (!self::$_instance) {
            throw new Exception('Instance is not set yet.');
        }
        return self::$_instance;
    }

    /**
     * Compose PHPUnit's data sets that contain each file as the first argument
     *
     * @param array $files
     * @return array
     */
    public static function composeDataSets(array $files)
    {
        $result = array();
        foreach ($files as $file) {
            /* Use filename as a data set name to not include it to every assertion message */
            $result[$file] = array($file);
        }
        return $result;
    }

    /**
     * Set path to source code
     *
     * @param string $pathToSource
     */
    public function __construct($pathToSource)
    {
        $this->_path = $pathToSource;
    }

    /**
     * Getter for _path
     *
     * @return string
     */
    public function getPathToSource()
    {
        return $this->_path;
    }

    /**
     * Returns array of PHP-files, that use or declare Magento application classes and Magento libs
     *
     * @param bool $appCode   application PHP-code
     * @param bool $otherCode non-application PHP-code (doesn't include "dev" directory)
     * @param bool $templates application PHTML-code
     * @param bool $asDataSet
     * @return array
     */
    public function getPhpFiles($appCode = true, $otherCode = true, $templates = true, $asDataSet = true)
    {
        $key = __METHOD__ . "/{$this->_path}/{$appCode}/{$otherCode}/{$templates}";
        if (!isset(self::$_cache[$key])) {
            $namespace = $module = $area = $package = $theme = '*';

            $files = array();
            if ($appCode) {
                $files = array_merge(
                    glob($this->_path . '/app/*.php', GLOB_NOSORT),
                    self::_getFiles(array("{$this->_path}/app/code/{$namespace}/{$module}"), '*.php')
                );
            }
            if ($otherCode) {
                $files = array_merge(
                    $files,
                    glob($this->_path . '/*.php', GLOB_NOSORT),
                    glob($this->_path . '/pub/*.php', GLOB_NOSORT),
                    self::_getFiles(array("{$this->_path}/downloader"), '*.php'),
                    self::_getFiles(array("{$this->_path}/lib/{Mage,Magento,Varien}"), '*.php')
                );
            }
            if ($templates) {
                $files = array_merge(
                    $files,
                    self::_getFiles(array("{$this->_path}/app/code/{$namespace}/{$module}"), '*.phtml'),
                    self::_getFiles(
                        array("{$this->_path}/app/design/{$area}/{$package}/{$theme}/{$namespace}_{$module}"),
                        '*.phtml'
                    )
                );
            }
            self::$_cache[$key] = $files;
        }

        if ($asDataSet) {
            return self::composeDataSets(self::$_cache[$key]);
        }
        return self::$_cache[$key];
    }

    /**
     * Returns list of files, where expected to have class declarations
     *
     * @return array
     */
    public function getClassFiles()
    {
        $key = __METHOD__ . $this->_path;
        if (isset(self::$_cache[$key])) {
            return self::$_cache[$key];
        }
        if (!isset(self::$_cache[$key])) {
            $files = array_merge(
                self::_getFiles(array("{$this->_path}/app/code/Magento"), '*.php'),
                //self::_getFiles(array("{$this->_path}/dev"), '*.php'),
                self::_getFiles(array("{$this->_path}/dev/tools/Magento"), '*.php'),
                self::_getFiles(array("{$this->_path}/downloader/app/Magento"), '*.php'),
                self::_getFiles(array("{$this->_path}/downloader/lib/Magento"), '*.php'),
                self::_getFiles(array("{$this->_path}/lib/Magento"), '*.php')
            );
        }
        $result = self::composeDataSets($files);
        self::$_cache[$key] = $result;
        return $result;
    }

    /**
     * Returns list of xml files, used by Magento application
     *
     * @return array
     */
    public function getXmlFiles()
    {
        return array_merge(
            self::getConfigFiles(),
            self::getLayoutFiles()
        );
    }

    /**
     * Returns list of configuration files, used by Magento application
     *
     * @param string $fileNamePattern
     * @param array $excludedFileNames
     * @param bool $asDataSet
     * @return array
     */
    public function getConfigFiles(
        $fileNamePattern = '*.xml',
        $excludedFileNames = array('wsdl.xml', 'wsdl2.xml', 'wsi.xml'),
        $asDataSet = true
    ) {
        $cacheKey = __METHOD__ . '|' . $this->_path . '|' . serialize(func_get_args());
        if (!isset(self::$_cache[$cacheKey])) {
            $files = glob($this->_path . "/app/code/*/*/etc/$fileNamePattern", GLOB_NOSORT | GLOB_BRACE);
            $files = array_filter(
                $files,
                function ($file) use ($excludedFileNames) {
                    return !in_array(basename($file), $excludedFileNames);
                }
            );
            self::$_cache[$cacheKey] = $files;
        }
        if ($asDataSet) {
            return self::composeDataSets(self::$_cache[$cacheKey]);
        }
        return self::$_cache[$cacheKey];
    }

    /**
     * Returns list of layout files, used by Magento application modules
     *
     * An incoming array can contain the following items
     * array (
     *     'namespace'      => 'namespace_name',
     *     'module'         => 'module_name',
     *     'area'           => 'area_name',
     *     'theme'          => 'theme_name',
     *     'include_code'   => true|false,
     *     'include_design' => true|false,
     * )
     *
     * @param array $incomingParams
     * @param bool $asDataSet
     * @return array
     */
    public function getLayoutFiles($incomingParams = array(), $asDataSet = true)
    {
        $params = array(
            'namespace' => '*',
            'module' => '*',
            'area' => '*',
            'theme' => '*',
            'include_code' => true,
            'include_design' => true
        );
        foreach (array_keys($params) as $key) {
            if (isset($incomingParams[$key])) {
                $params[$key] = $incomingParams[$key];
            }
        }
        $cacheKey = md5($this->_path . '|' . implode('|', $params));

        if (!isset(self::$_cache[__METHOD__][$cacheKey])) {
            $files = array();
            if ($params['include_code']) {
                $files = self::_getFiles(
                    array(
                        "{$this->_path}/app/code/{$params['namespace']}/{$params['module']}"
                        . "/view/{$params['area']}/layout"
                    ),
                    '*.xml'
                );
            }
            if ($params['include_design']) {
                $themeLayoutDir = "{$this->_path}/app/design/{$params['area']}/{$params['theme']}"
                    . "/{$params['namespace']}_{$params['module']}/layout";
                $dirPatterns = array(
                    $themeLayoutDir,
                    $themeLayoutDir . '/override',
                    $themeLayoutDir . '/override/*/*',
                );
                $files = array_merge(
                    $files,
                    self::_getFiles(
                        $dirPatterns,
                        '*.xml'
                    )
                );
            }
            self::$_cache[__METHOD__][$cacheKey] = $files;
        }

        if ($asDataSet) {
            return self::composeDataSets(self::$_cache[__METHOD__][$cacheKey]);
        }
        return self::$_cache[__METHOD__][$cacheKey];
    }

    /**
     * Returns list of Javascript files in Magento
     *
     * @return array
     */
    public function getJsFiles()
    {
        $key = __METHOD__ . $this->_path;
        if (isset(self::$_cache[$key])) {
            return self::$_cache[$key];
        }
        $namespace = $module = $area = $package = $theme = $skin = '*';
        $files = self::_getFiles(
            array(
                "{$this->_path}/app/code/{$namespace}/{$module}/view/{$area}",
                "{$this->_path}/app/design/{$area}/{$package}/{$theme}/skin/{$skin}",
                "{$this->_path}/pub/lib/{mage,varien}"
            ),
            '*.js'
        );
        $result = self::composeDataSets($files);
        self::$_cache[$key] = $result;
        return $result;
    }

    /**
     * Returns list of Twig files in Magento app directory.
     *
     * @return array
     */
    public function getTwigFiles()
    {
        return self::_getFiles(array($this->_path . '/app'), '*.twig');
    }

    /**
     * Returns list of Phtml files in Magento app directory.
     *
     * @return array
     */
    public function getPhtmlFiles()
    {
        return $this->getPhpFiles(false, false, true, true);
    }

    /**
     * Returns list of email template files
     *
     * @return array
     */
    public function getEmailTemplates()
    {
        $key = __METHOD__ . $this->_path;
        if (isset(self::$_cache[$key])) {
            return self::$_cache[$key];
        }
        $files = self::_getFiles(array($this->_path . '/app/code/*/*/view/email'), '*.html');
        $result = self::composeDataSets($files);
        self::$_cache[$key] = $result;
        return $result;
    }

    /**
     * Return list of all files. The list excludes tool-specific files
     * (e.g. Git, IDE) or temp files (e.g. in "var/").
     *
     * @return array
     */
    public function getAllFiles()
    {
        $key = __METHOD__ . $this->_path;
        if (isset(self::$_cache[$key])) {
            return self::$_cache[$key];
        }

        $subFiles = self::_getFiles(
            array(
                $this->_path . '/app',
                $this->_path . '/dev',
                $this->_path . '/downloader',
                $this->_path . '/lib',
                $this->_path . '/pub',
            ),
            '*'
        );

        $rootFiles = glob($this->_path . '/*', GLOB_NOSORT);
        $rootFiles = array_filter(
            $rootFiles,
            function ($file) {
                return is_file($file);
            }
        );

        $result = array_merge($rootFiles, $subFiles);
        $result = self::composeDataSets($result);

        self::$_cache[$key] = $result;
        return $result;
    }

    /**
     * Retrieve all files in folders and sub-folders that match pattern (glob syntax)
     *
     * @param array $dirPatterns
     * @param string $fileNamePattern
     * @return array
     */
    protected static function _getFiles(array $dirPatterns, $fileNamePattern)
    {
        $result = array();
        foreach ($dirPatterns as $oneDirPattern) {
            $entriesInDir = glob("$oneDirPattern/$fileNamePattern", GLOB_NOSORT | GLOB_BRACE);
            $subDirs = glob("$oneDirPattern/*", GLOB_ONLYDIR | GLOB_NOSORT | GLOB_BRACE);
            $filesInDir = array_diff($entriesInDir, $subDirs);

            $filesInSubDir = self::_getFiles($subDirs, $fileNamePattern);
            $result = array_merge($result, $filesInDir, $filesInSubDir);
        }
        return $result;
    }

    /**
     * Look for DI config through the system
     * @return array
     */
    public function getDiConfigs()
    {
        $primaryConfigs = glob($this->_path . '/app/etc/di/*.xml');
        $moduleConfigs = glob($this->_path . '/app/code/*/*/etc/{di,*/di}.xml', GLOB_BRACE);
        $configs = array_merge($primaryConfigs, $moduleConfigs);
        return $configs;
    }

    /**
     * Check if specified class exists
     *
     * @param string $class
     * @param string &$path
     * @return bool
     */
    public function classFileExists($class, &$path = '')
    {
        if ($class[0] == '\\') {
            $class = substr($class, 1);
        }
        $classParts = explode('\\', $class);
        $className = array_pop($classParts);
        $nameSpace = implode('\\', $classParts);
        $path = implode(DIRECTORY_SEPARATOR, explode('\\', $class)) . '.php';
        $directories = array('/app/code/', '/lib/');
        foreach ($directories as $dir) {
            $fullPath = str_replace('/', DIRECTORY_SEPARATOR, $this->_path . $dir . $path);
            /**
             * Use realpath() instead of file_exists() to avoid incorrect work on Windows because of case insensitivity
             * of file names
             */
            if (realpath($fullPath) == $fullPath) {
                $fileContent = file_get_contents($fullPath);
                if (strpos($fileContent, 'namespace ' . $nameSpace) !== false &&
                    (strpos($fileContent, 'class ' . $className) !== false ||
                        strpos($fileContent, 'interface ' . $className) !== false)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Return list of declared namespaces
     *
     * @return array
     */
    public function getNamespaces()
    {
        $key = __METHOD__ . $this->_path;
        if (isset(self::$_cache[$key])) {
            return self::$_cache[$key];
        }

        $iterator = new DirectoryIterator($this->_path . '/app/code/');
        $result = array();
        foreach ($iterator as $file) {
            if (!$file->isDot() && !in_array($file->getFilename(), array('Zend')) && $file->isDir()) {
                $result[] = $file->getFilename();
            }
        }

        self::$_cache[$key] = $result;
        return $result;
    }

    /**
     * @param string $namespace
     * @param string $module
     * @param string $file
     * @return string
     */
    public function getModuleFile($namespace, $module, $file)
    {
        return $this->_path . DIRECTORY_SEPARATOR
            . 'app'. DIRECTORY_SEPARATOR
            . 'code'. DIRECTORY_SEPARATOR
            . $namespace . DIRECTORY_SEPARATOR
            . $module . DIRECTORY_SEPARATOR
            . $file;
    }
}
