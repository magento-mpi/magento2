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
     * Dry run flag
     *
     * @var bool
     */
    public static $dryRun = false;

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
        foreach ($paths as $resource) {
            if (is_file($resource) && !in_array(realpath($resource), self::$skipFiles)) {
                $result[] = $resource;
                continue;
            }
            if (self::_isDirectorySkipped($resource)) {
                continue;
            }
            if ($allowRecursion) {
                self::globSearch(glob($resource . '/*', GLOB_ONLYDIR), $fileMasks, $result, true, true);
            }
            foreach ($fileMasks as $filesMask) {
                self::_filterFiles($resource, $filesMask, $result);
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
     * @param bool $recursive
     * @return void
     */
    public static function updateLicense($directories, $fileMasks, $license, $recursive = true)
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
                if (!self::$dryRun) {
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
                self::printLog("Can't access license file: {$licenseClassFile}.\n");
                return false;
            }

            include_once $licenseClassFile;

            if (!class_exists($licenseClassName)) {
                self::printLog("Can't find license class: {$licenseClassName}.\n");
                return false;
            }
        }

        $licenseObject = new $licenseClassName;

        if (!$licenseObject instanceof LicenseAbstract) {
            self::printLog("License class does not have correct interface: {$licenseClassName}.\n");
            return false;
        }

        return $licenseObject;
    }

    public static function run($config, $workingDir)
    {
        $licenseInstances = array();

        foreach ($config as $dir => $types) {
            $dirs = array($workingDir . DIRECTORY_SEPARATOR . $dir);
            // Extract params for directory
            $recursive = true;
            if (isset($types['_params'])) {
                $params = $types['_params'];
                unset($types['_params']);
                if (isset($params['recursive'])) {
                    $recursive = $params['recursive'];
                }

                // Exclude skipped files or directories
                if (isset($params['skipped'])) {
                    // Exclude directories
                    if (isset($params['skipped']['dir'])) {
                        if (!is_array($params['skipped']['dir'])) {
                            $params['skipped']['dir'] = array($params['skipped']['dir']);
                        }
                        foreach($params['skipped']['dir'] as $key=>$excludeDir) {
                            $params['skipped']['dir'][$key] = '!' . $excludeDir;
                        }
                        $dirs = array_merge($dirs, $params['skipped']['dir']);
                    }

                    // Exclude files
                    if (isset($params['skipped']['file'])) {
                        if (!is_array($params['skipped']['file'])) {
                            $params['skipped']['file'] = array($params['skipped']['file']);
                        }
                        foreach($params['skipped']['file'] as $key=>$excludeFile) {
                            $params['skipped']['file'][$key] = '!' . $excludeFile;
                        }
                        $dirs = array_merge($dirs, $params['skipped']['file']);
                    }
                }
                unset($params);
            }

            // Process types
            foreach ($types as $type => $licenseType) {
                if (!isset($licenseInstances[$licenseType])) {
                    $licenseInstances[$licenseType] = Routine::createLicenseInstance($licenseType);
                    if (!$licenseInstances[$licenseType]) {
                        exit(1);
                    }
                }
                Routine::updateLicense($dirs, Routine::$fileTypes[$type], $licenseInstances[$licenseType], $recursive);
            }
        }
    }
}
