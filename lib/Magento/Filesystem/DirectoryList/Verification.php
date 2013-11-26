<?php
/**
 * The class, which verifies existence and write access to the needed application directories
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Filesystem\DirectoryList;

use Magento\Filesystem\FilesystemException;

class Verification
{
    /**
     * Codes of directories to create and verify in production mode
     *
     * @var array
     */
    protected static $productionDirs = array(
        \Magento\Filesystem::MEDIA,
        \Magento\Filesystem::VAR_DIR,
        \Magento\Filesystem::TMP,
        \Magento\Filesystem::CACHE,
        \Magento\Filesystem::LOG,
        \Magento\Filesystem::SESSION,
    );

    /**
     * Codes of directories to create and verify in non-production mode
     *
     * @var array
     */
    protected static $nonProductionDirs = array(
        \Magento\Filesystem::MEDIA,
        \Magento\Filesystem::STATIC_VIEW,
        \Magento\Filesystem::VAR_DIR,
        \Magento\Filesystem::TMP,
        \Magento\Filesystem::CACHE,
        \Magento\Filesystem::LOG,
        \Magento\Filesystem::SESSION,
    );

    /**
     * @var \Magento\Filesystem
     */
    protected $filesystem;

    /**
     * Cached list of directories to create and verify write access
     *
     * @var array
     */
    protected $dirsToVerify = array();

    /**
     * Constructor - initialize object with required dependencies, determine application state
     *
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\App\State $appState
     */
    public function __construct(
        \Magento\Filesystem $filesystem,
        \Magento\App\State $appState
    ) {
        $this->filesystem = $filesystem;
        $this->dirsToVerify = $this->_getDirsToVerify($appState);
    }

    /**
     * Return list of directories, that must be verified according to the application mode
     *
     * @param \Magento\App\State $appState
     * @return array
     */
    protected function _getDirsToVerify(\Magento\App\State $appState)
    {
        $codes = ($appState->getMode() == \Magento\App\State::MODE_PRODUCTION)
            ? self::$productionDirs
            : self::$nonProductionDirs;
        return $codes;
    }

    /**
     * Create the required directories, if they don't exist, and verify write access for existing directories
     */
    public function createAndVerifyDirectories()
    {
        $fails = array();
        foreach ($this->dirsToVerify as $code) {
            $directory = $this->filesystem->getDirectoryWrite($code);
            if ($directory->isExist()) {
                if (!$directory->isWritable()) {
                    $fails[] = $directory->getAbsolutePath();
                }
            } else {
                try {
                    $directory->create();
                } catch (FilesystemException $e) {
                    $fails[] = $directory->getAbsolutePath();
                }
            }
        }

        if ($fails) {
            $dirList = implode(', ', $fails);
            throw new \Magento\BootstrapException(
                "Cannot create or verify write access: {$dirList}"
            );
        }
    }
}
