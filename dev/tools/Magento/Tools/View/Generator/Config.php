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
     * @param \Magento\Framework\App\Filesystem $filesystem
     * @param array $cmdOptions
     * @param array $allowedFiles Non-generated files delivered with the application,
     *     so allowed to be present in the publication directory
     * @throws \Magento\Framework\Exception
     */
    public function __construct(
        \Magento\Framework\App\Filesystem $filesystem,
        array $cmdOptions,
        $allowedFiles = array()
    ) {
        $rootDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem::ROOT_DIR);
        $sourceDir = isset($cmdOptions['source']) ? $cmdOptions['source'] : $rootDirectory->getAbsolutePath();
        if (!$rootDirectory->isDirectory($rootDirectory->getRelativePath($sourceDir))) {
            throw new \Magento\Framework\Exception('Source directory does not exist: ' . $sourceDir);
        }

        if (isset($cmdOptions['destination'])) {
            $destinationDir = $cmdOptions['destination'];
        } else {
            $destinationDir = $filesystem->getPath(\Magento\Framework\App\Filesystem::STATIC_VIEW_DIR);
        }
        $destinationDirRelative = $rootDirectory->getRelativePath($destinationDir);
        if (!$rootDirectory->isDirectory($destinationDirRelative)) {
            throw new \Magento\Framework\Exception('Destination directory does not exist: ' . $destinationDir);
        }
        foreach ($allowedFiles as $k => $allowedFile) {
            $allowedFiles[$k] = $destinationDirRelative . '/' . $allowedFile;
        }
        if (array_diff($rootDirectory->read($destinationDirRelative), $allowedFiles)) {
            throw new \Magento\Framework\Exception("Destination directory must be empty: {$destinationDir}");
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
