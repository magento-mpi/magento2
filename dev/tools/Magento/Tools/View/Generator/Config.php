<?php
/**
 * {license_notice}
 *
 * @category   Tools
 * @package    view
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Parses, verifies and stores command-line parameters
 */
namespace Magento\Tools\View\Generator;

class Config
{
    /**
     * @var string
     */
    private $_sourceDir;

    /**
     * @var string
     */
    private $_destinationDir;

    /**
     * @var bool
     */
    private $_isDryRun;

    /**
     * @param \Magento\Filesystem $filesystem
     * @param array $cmdOptions
     * @throws \Magento\Exception
     */
    public function __construct(\Magento\Filesystem $filesystem, $cmdOptions)
    {
        $rootDirectory = $filesystem->getDirectoryWrite(\Magento\Filesystem::ROOT);
        $sourceDir = isset($cmdOptions['source']) ? $cmdOptions['source'] : $rootDirectory->getAbsolutePath();
        if (!$rootDirectory->isDirectory($rootDirectory->getRelativePath($sourceDir))) {
            throw new \Magento\Exception('Source directory does not exist: ' . $sourceDir);
        }

        if (isset($cmdOptions['destination'])) {
            $destinationDir = $cmdOptions['destination'];
        } else {
            $destinationDir = $filesystem->getPath(\Magento\Filesystem::STATIC_VIEW);
        }
        $destinationDirRelative = $rootDirectory->getRelativePath($destinationDir);
        if (!$rootDirectory->isDirectory($destinationDirRelative)) {
            throw new \Magento\Exception('Destination directory does not exist: ' . $destinationDir);
        }
        if ($rootDirectory->read($destinationDirRelative)) {
            throw new \Magento\Exception("Destination directory must be empty: {$destinationDir}");
        }

        $isDryRun = isset($cmdOptions['dry-run']);

        // Assign to internal values
        $this->_sourceDir = $sourceDir;
        $this->_destinationDir = $destinationDir;
        $this->_isDryRun = $isDryRun;
    }

    /**
     * Return configured source path
     *
     * @return string
     */
    public function getSourceDir()
    {
        return $this->_sourceDir;
    }

    /**
     * Return configured destination path
     *
     * @return string
     */
    public function getDestinationDir()
    {
        return $this->_destinationDir;
    }

    /**
     * Return, whether dry run is turned on
     *
     * @return bool
     */
    public function isDryRun()
    {
        return $this->_isDryRun;
    }
}
