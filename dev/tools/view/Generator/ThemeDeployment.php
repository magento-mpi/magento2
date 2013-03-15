<?php
/**
 * {license_notice}
 *
 * @category   Tools
 * @package    translate
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Transformation of files, which must be copied to new location and its contents processed
 */
class Generator_ThemeDeployment
{
    /**
     * @var Zend_Log
     */
    private $_logger;

    /**
     * List of extensions for files, which should be published.
     * For efficiency it is a map of ext => ext, so lookup by hash is possible.
     *
     * @var array
     */
    private $_permitted;

    /**
     * List of extensions for files, which must not be published
     * For efficiency it is a map of ext => ext, so lookup by hash is possible.
     *
     * @var array
     */
    private $_forbidden;

    /**
     * Constructor
     *
     * @param Zend_Log $logger
     * @param string $configPermitted
     * @param string $configForbidden
     * @throws Magento_Exception
     */
    public function __construct(Zend_Log $logger, $configPermitted, $configForbidden)
    {
        $this->_logger = $logger;

        $this->_permitted = $this->_loadConfig($configPermitted);
        $this->_forbidden = $this->_loadConfig($configForbidden);
        $this->_forbidden[''] = '';
        $conflicts = array_intersect($this->_permitted, $this->_forbidden);
        if ($conflicts) {
            $message = 'The following extensions are both added to permitted and forbidden lists: %s';
            throw new Magento_Exception(sprintf($message, implode(', ', $conflicts)));
        }
    }

    /**
     * Load config with file extensions
     *
     * @param string $path
     * @return array
     * @throws Magento_Exception
     */
    protected function _loadConfig($path)
    {
        if (!file_exists($path)) {
            throw new Magento_Exception("Config file does not exist: {$path}");
        }

        $contents = explode("\n", file_get_contents($path));
        $contents = array_unique($contents);

        foreach ($contents as $key => $line) {
            if ((substr($line, 0, 2) == '//') || !strlen($line)) {
                unset($contents[$key]);
            }
        }

        $result = $contents ? array_combine($contents, $contents) : array();
        return $result;
    }

    /**
     * Copy all the files according to $copyRules, which contains pairs of 'source' and 'destination' directories
     *
     * @param array $copyRules
     * @param string $destinationDir
     * @param bool $isDryRun
     */
    public function run($copyRules, $destinationDir, $isDryRun = false)
    {
        if ($isDryRun) {
            $this->_log('Running in dry-run mode');
        }

        foreach ($copyRules as $copyRule) {
            $this->_copyDirStructure(
                $copyRule['source'],
                $destinationDir . DIRECTORY_SEPARATOR . $copyRule['destination'],
                $isDryRun
            );
        }
    }


    /**
     * Copy dir structure and files from $sourceDir to $destinationDir
     *
     * @param string $sourceDir
     * @param string $destinationDir
     * @param bool $isDryRun
     * @throws Magento_Exception
     */
    protected function _copyDirStructure($sourceDir, $destinationDir, $isDryRun)
    {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($sourceDir));
        foreach ($files as $fileSource) {
            $extension = pathinfo($fileSource, PATHINFO_EXTENSION);
            if (isset($this->_forbidden[$extension])) {
                continue;
            }
            if (!isset($this->_permitted[$extension])) {
                $message = 'The file extension "%s" must be added either to the permitted or forbidden list. File: %s';
                throw new Magento_Exception(sprintf($message, $extension, $fileSource));
            }
            $fileDestination = $destinationDir . substr($fileSource, strlen($sourceDir));
            $this->_publishFile($fileSource, $fileDestination, $isDryRun);
        }
    }

    /**
     * Publish file to the destination path, also processing paths inside css-files.
     *
     * @param string $fileSource
     * @param string $fileDestination
     * @param bool $isDryRun
     */
    protected function _publishFile($fileSource, $fileDestination, $isDryRun)
    {
        // Create directory
        $dir = dirname($fileDestination);
        if (!is_dir($dir)) {
            mkdir($dir, 0666, true);
        }

        // Copy file, with additional relative urls processing for css
        $extension = strtolower(pathinfo($fileSource, PATHINFO_EXTENSION));
        if ($extension == 'css') {
            $this->_log($fileSource . ' ==CSS==> ' . $fileDestination);
            $content = $this->_processCssContent($fileSource);
            if (!$isDryRun) {
                file_put_contents($fileDestination, $content);
            }
        } else {
            $this->_log($fileSource . ' => ' . $fileDestination);
            if (!$isDryRun) {
                copy($fileSource, $fileDestination);
            }
        }
    }

    /**
     * Processes CSS file contents, replacing relative and modular urls to the appropriate values
     *
     * @param string $filePath
     * @return string
     */
    protected function _processCssContent($filePath)
    {
        // Not implemented yet
        return file_get_contents($filePath);
    }

    /**
     * Log message, using the logger object
     *
     * @param string $message
     */
    protected function _log($message)
    {
        $this->_logger->log($message, Zend_Log::INFO);
    }
}
