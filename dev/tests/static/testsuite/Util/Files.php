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
class Util_Files
{
    /**
     * In-memory cache for the data sets
     *
     * @var array
     */
    protected static $_cache = array();

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
     * Returns array of PHP-files, that use or declare Magento application classes and Magento libs
     *
     * @return array
     */
    public static function getPhpFiles()
    {
        if (isset(self::$_cache[__METHOD__])) {
            return self::$_cache[__METHOD__];
        }
        $root = PATH_TO_SOURCE_CODE;
        $pool = $namespace = $module = $area = $package = $theme = '*';
        $files = array_merge(
            glob($root . '/{app,pub}/*.php', GLOB_NOSORT | GLOB_BRACE),
            self::_getFiles(array("{$root}/app/code/{$pool}/{$namespace}/{$module}"), '*.{php,phtml}'),
            self::_getFiles(array("{$root}/app/design/{$area}/{$package}/{$theme}/{$namespace}_{$module}"), '*.phtml'),
            self::_getFiles(array("{$root}/downloader"), '*.php'),
            self::_getFiles(array("{$root}/lib/{Mage,Magento,Varien}"), '*.php')
        );
        $result = self::composeDataSets($files);
        self::$_cache[__METHOD__] = $result;
        return $result;
    }

    /**
     * Returns list of xml files, used by Magento application
     *
     * @return array
     */
    public static function getXmlFiles()
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
     * @return array
     */
    public static function getConfigFiles(
        $fileNamePattern = '*.xml', $excludedFileNames = array('wsdl.xml', 'wsdl2.xml', 'wsi.xml')
    ) {
        $cacheKey = __METHOD__ . '|' . serialize(func_get_args());
        if (isset(self::$_cache[$cacheKey])) {
            return self::$_cache[$cacheKey];
        }
        $files = glob(PATH_TO_SOURCE_CODE . "/app/code/*/*/*/etc/$fileNamePattern", GLOB_NOSORT | GLOB_BRACE);
        $files = array_filter($files, function ($file) use ($excludedFileNames) {
            return !in_array(basename($file), $excludedFileNames);
        });
        $result = self::composeDataSets($files);
        self::$_cache[$cacheKey] = $result;
        return $result;
    }

    /**
     * Returns list of layout files, used by Magento application modules
     *
     * @return array
     */
    public static function getLayoutFiles()
    {
        if (isset(self::$_cache[__METHOD__])) {
            return self::$_cache[__METHOD__];
        }
        $root = PATH_TO_SOURCE_CODE;
        $pool = $namespace = $module = $area = $package = $theme = '*';
        $files = array_merge(
            self::_getFiles(
                array(
                    "{$root}/app/code/{$pool}/{$namespace}/{$module}/view/{$area}",
                    "{$root}/app/design/{$area}/{$package}/{$theme}/{$namespace}_{$module}",
                ),
                '*.xml'
            ),
            glob("{$root}/app/design/{$area}/{$package}/{$theme}/local.xml", GLOB_NOSORT)
        );
        $result = self::composeDataSets($files);
        self::$_cache[__METHOD__] = $result;
        return $result;
    }

    /**
     * Returns list of Javascript files in Magento
     *
     * @return array
     */
    public static function getJsFiles()
    {
        if (isset(self::$_cache[__METHOD__])) {
            return self::$_cache[__METHOD__];
        }
        $root = PATH_TO_SOURCE_CODE;
        $pool = $namespace = $module = $area = $package = $theme = $skin = '*';
        $files = self::_getFiles(
            array(
                "{$root}/app/code/{$pool}/{$namespace}/{$module}/view/{$area}",
                "{$root}/app/design/{$area}/{$package}/{$theme}/skin/{$skin}",
                "{$root}/pub/js/{mage,varien}"
            ),
            '*.js'
        );
        $result = self::composeDataSets($files);
        self::$_cache[__METHOD__] = $result;
        return $result;
    }

    /**
     * Returns list of email template files
     *
     * @return array
     */
    public static function getEmailTemplates()
    {
        if (isset(self::$_cache[__METHOD__])) {
            return self::$_cache[__METHOD__];
        }
        $files = self::_getFiles(array(PATH_TO_SOURCE_CODE . '/app/code/*/*/*/view/email'), '*.html');
        $result = self::composeDataSets($files);
        self::$_cache[__METHOD__] = $result;
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
            $filesInDir = glob("$oneDirPattern/$fileNamePattern", GLOB_NOSORT | GLOB_BRACE);
            $subDirs = glob("$oneDirPattern/*", GLOB_ONLYDIR | GLOB_NOSORT | GLOB_BRACE);
            $filesInSubDir = self::_getFiles($subDirs, $fileNamePattern);
            $result = array_merge($result, $filesInDir, $filesInSubDir);
        }
        return $result;
    }
}
