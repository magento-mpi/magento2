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
     * @param string $appBaseDir
     * @param array $cmdOptions
     * @throws \Magento\Exception
     */
    public function __construct($appBaseDir, $cmdOptions)
    {
        $sourceDir = isset($cmdOptions['source']) ? $cmdOptions['source'] : $appBaseDir;
        if (!is_dir($sourceDir)) {
            throw new \Magento\Exception('Source directory does not exist: ' . $sourceDir);
        }

        if (isset($cmdOptions['destination'])) {
            $destinationDir = $cmdOptions['destination'];
        } else {
            $dirs = new \Magento_Core_Model_Dir($sourceDir);
            $destinationDir = $dirs->getDir(\Magento_Core_Model_Dir::STATIC_VIEW);
        }
        if (!is_dir($destinationDir)) {
            throw new \Magento\Exception('Destination directory does not exist: ' . $destinationDir);
        }
        if (glob($destinationDir . DIRECTORY_SEPARATOR . '*')) {
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
