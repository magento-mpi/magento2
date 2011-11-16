<?php
/**
 * Service routines for license-tool command line script
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   build
 * @package    license
 * @copyright  Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Routine run time functions
 *
 */
class Routine
{
    /**
     * List skipped directories
     *
     * @var null|array
     */
    public static $skipDirectories = null;

    /**
     * List skipped files
     *
     * @var null|array
     */
    public static $skipFiles = null;

    /**
     * Verbose output flag
     *
     * @var bool
     */
    public static $isVerbose = false;

    /**
     * File types
     *
     * @var array
     */
    public static $fileTypes = array(
        'xml'   => array('*.xml', '*.xml.template', '*.xml.additional', '*.xml.dist', '*.xml.sample', '*.xsd'),
        'php'   => array('*.php'),
        'phtml' => array('*.phtml'),
        'css'   => array('*.css'),
        'js'    => array('*.js')
    );

    /**
     * Walk through all file inside folder and sub folder. Filter found files by pattern.
     *
     * @static
     * @param string|array $paths
     * @param string|array $fileMasks
     * @param array $result
     * @param bool $allowRecursion
     * @param bool $inRecursion
     * @return null
     */
    public static function globSearch($paths, $fileMasks, &$result, $allowRecursion = true, $inRecursion = false)
    {
        if (empty($paths)) {
            return;
        }

        if (!is_array($paths)) {
            $paths = array($paths);
        }

        if (!is_array($fileMasks)) {
            $fileMasks = array($fileMasks);
        }

        if (!$inRecursion) {
            self::$skipDirectories = array();
            self::$skipFiles = array();
            $paths = self::_filterPaths($paths);
        }
        foreach ($paths as $dir) {
            if (self::_isDirectorySkipped($dir)) {
                continue;
            }
            if ($allowRecursion) {
                self::globSearch(glob($dir . '/*', GLOB_ONLYDIR), $fileMasks, $result, true, true);
            }
            foreach ($fileMasks as $filesMask) {
                self::_filterFiles($dir, $filesMask, $result);
            }
        }
    }

    /**
     * Filters passed array on skip path items marked by "!" sign
     *
     * @static
     * @param array $paths
     * @return array
     */
    protected static function _filterPaths($paths)
    {
        foreach ($paths as $k => $path) {
            if (false !== strpos($path, '!')) {
                $real = realpath(str_replace('!', '', $path));
                if (is_dir($real)) {
                    self::$skipDirectories[] = $real;
                } elseif (is_file($real)) {
                    self::$skipFiles[] = $real;
                }
                unset($paths[$k]);
            }
        }

        return $paths;
    }

    /**
     * Filter directory by passed file mask. Results will be saved in $result variable.
     *
     * @static
     * @param string $directory
     * @param array $filesMask
     * @param array $result
     * @return null
     */
    protected static function _filterFiles($directory, $filesMask, &$result)
    {
        foreach (glob($directory . '/' . $filesMask) as $filename) {
            if (!in_array(realpath($filename), self::$skipFiles)) {
                $result[] = $filename;
            }
        }
    }

    /**
     * Analyzes passed directory should it be skipped or not.
     *
     * @static
     * @param string $directory
     * @return bool
     */
    protected static function _isDirectorySkipped($directory)
    {
        foreach (self::$skipDirectories as $skipDir) {
            if (false !== strpos(realpath($directory), $skipDir)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Updates files in passed directory using license rules.
     * Could be run as validation process for files in dry run case.
     *
     * @static
     * @param string|array $directories
     * @param string|array $fileMasks
     * @param AbstractLicense $license
     * @param bool $dryRun
     * @param bool $recursive
     * @return void
     */
    public static function updateLicense($directories, $fileMasks, $license, $dryRun = false, $recursive = true)
    {
        if (!is_array($directories)) {
            $directories = rtrim($directories, '/');
        } else {
            foreach ($directories as $key => $dir) {
                $directories[$key] = rtrim($dir, '/');
            }
        }

        $foundFiles = array();
        self::globSearch($directories, $fileMasks, $foundFiles, $recursive);

        foreach ($foundFiles as $filename) {
            self::printLog($filename . '...');
            $contents = file_get_contents($filename);

            preg_match('#/\*\*(.*)\*/.*#Us', $contents, $matches);

            if (!isset($matches[1])) {
                self::printLog("Failed!\n");
                continue;
            }

            $placeHolders = array(
                ' * {license_notice}',
                '{copyright}',
                '{license_link}'
            );

            $changeSet = array(
                $license->getNotice(),
                $license->getCopyright(),
                $license->getLink()
            );

            $docBlock = str_replace($placeHolders, $changeSet, $matches[1]);

            $newContents = preg_replace('#(/\*\*).*(\*/.*)#Us', '$1'. $docBlock . '$2', $contents, 1);

            if ($contents !== $newContents) {
                if (!$dryRun) {
                    file_put_contents($filename, $newContents);
                }
                self::printLog("Ok\n");
            } else {
                self::printLog("Failed!\n");
            }
        }
    }

    /**
     * Prints logging messaged in case verbose mode enabled during run.
     *
     * @statict
     * @param string $msg
     * @return null
     */
    public static function printLog($msg)
    {
        if (self::$isVerbose) {
            echo $msg;
        }
    }

    /**
     * Create instance of license class which contains information about license
     *
     * @statict
     * @param string $license
     * @return AbstractLicense
     */
    public static function createLicenseInstance($license)
    {
        $licenseClassName = ucfirst(strtolower($license));
        if (!class_exists($licenseClassName)) {
            $licenseClassFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . $licenseClassName . '.php';
            if (!file_exists($licenseClassFile) || !is_readable($licenseClassFile)) {
                self::printLog("Can't access license file.\n");
                return false;
            }

            include_once $licenseClassFile;

            if (!class_exists($licenseClassName)) {
                self::printLog("Can't find license class.\n");
                return false;
            }
        }

        $licenseObject = new $licenseClassName;

        if (!$licenseObject instanceof LicenseAbstract) {
            self::printLog("License class does not have correct interface.\n");
            return false;
        }

        return $licenseObject;
    }
}
