<?php
/**
 * {license_notice}
 *
 * @category   build
 * @package    license
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Service routines for license-tool command line script
 * Routine run time functions
 *
 */
class Routine
{
    /**
     * Flag signalized that some files are not updated
     *
     * @var bool
     */
    protected static $_isFailedUpdate = false;

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
        'php'   => array('*.php', '*.php.sample'),
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
     * @return null
     */
    public static function globSearch($paths, $fileMasks, &$result, $allowRecursion = true)
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

        foreach ($paths as $resource) {
            if (is_file($resource) && !self::_isFileSkipped($resource)) {
                $result[] = $resource;
                continue;
            }

            if (self::_isDirectorySkipped($resource)) {
                continue;
            }

            if ($allowRecursion) {
                self::globSearch(glob($resource . '/*', GLOB_ONLYDIR), $fileMasks, $result, true);
            }

            self::_filterFilesByMask($resource, $fileMasks, $result);
        }
    }

    /**
     * Filter directory by passed file mask. Results will be saved in $result variable.
     *
     * @static
     * @param $directory
     * @param $fileMasks
     * @param $result
     * @return null
     */
    protected static function _filterFilesByMask($directory, $fileMasks, &$result)
    {
        foreach ($fileMasks as $filesMask) {
            foreach (glob($directory . '/' . $filesMask) as $filename) {
                if (!self::_isFileSkipped($filename)) {
                    $result[] = $filename;
                }
            }
        }
    }

    /**
     * Filters passed array on skip path items marked by "!" sign
     *
     * @static
     * @param string $workingDir
     * @param array $paths
     * @return null
     */
    protected static function _setSkippedPaths($workingDir, $paths)
    {
        if (!is_array($paths)) {
            $paths = array($paths);
        }
        foreach ($paths as $path) {
            $real = realpath($workingDir . DIRECTORY_SEPARATOR . $path);
            if (is_dir($real)) {
                self::$skipDirectories[] = $real;
            } elseif (is_file($real)) {
                self::$skipFiles[] = $real;
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
        $directory = realpath($directory) . DIRECTORY_SEPARATOR;
        foreach (self::$skipDirectories as $skipDir) {
            if (false !== strpos($directory, $skipDir . DIRECTORY_SEPARATOR)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Analyzes passed file should it be skipped or not.
     *
     * @static
     * @param string $filename
     * @return bool
     */
    protected static function _isFileSkipped($filename)
    {
        return in_array(realpath($filename), self::$skipFiles);
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
     * @return null
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
                self::$_isFailedUpdate = true;
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
                self::$_isFailedUpdate = true;
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
     * @throws Exception
     * @param string $license
     * @return AbstractLicense
     */
    public static function createLicenseInstance($license)
    {
        $licenseClassName = ucfirst(strtolower($license));
        if (!class_exists($licenseClassName)) {
            $licenseClassFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . $licenseClassName . '.php';
            if (!file_exists($licenseClassFile) || !is_readable($licenseClassFile)) {
                throw new Exception("Can't access license file: {$licenseClassFile}.\n");
            }

            include_once $licenseClassFile;

            if (!class_exists($licenseClassName)) {
                throw new Exception("Can't find license class: {$licenseClassName}.\n");
            }
        }

        $licenseObject = new $licenseClassName;

        if (!$licenseObject instanceof LicenseAbstract) {
            throw new Exception("License class does not have correct interface: {$licenseClassName}.\n");
        }

        return $licenseObject;
    }

    /**
     * Entry point of routine work
     *
     * @static
     * @param $config
     * @param $workingDir
     * @return null
     */
    public static function run($config, $workingDir)
    {
        $licenseInstances = array();

        foreach ($config as $path => $types) {
            $paths = array($workingDir . DIRECTORY_SEPARATOR . $path);
            // Extract params for directory
            $recursive = true;
            self::$skipFiles = array();
            self::$skipDirectories = array();

            if (isset($types['_params'])) {
                if (isset($types['_params']['recursive'])) {
                    $recursive = $types['_params']['recursive'];
                }

                if (isset($types['_params']['skipped'])) {
                    self::_setSkippedPaths($workingDir, $types['_params']['skipped']);
                }
                unset($types['_params']);
            }

            // Process types
            foreach ($types as $fileType => $licenseType) {
                if (!isset($licenseInstances[$licenseType])) {
                    $licenseInstances[$licenseType] = Routine::createLicenseInstance($licenseType);
                }
                Routine::updateLicense(
                    $paths,
                    Routine::$fileTypes[$fileType],
                    $licenseInstances[$licenseType],
                    $recursive
                );
            }
        }

        if (self::$_isFailedUpdate) {
            throw new Exception("Failed during updating files.\n");
        }
    }
}
