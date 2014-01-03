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

use Magento\App\State,
    Magento\Filesystem,
    Magento\Filesystem\FilesystemException,
    Magento\App\Filesystem as AppFilesystem;

class Verification
{
    /**
     * Codes of directories to create and verify in production mode
     *
     * @TODO temporary solution for constants
     *
     * @var array
     */
    protected static $productionDirs = array(
        AppFilesystem::MEDIA_DIR,
        AppFilesystem::VAR_DIR,
        AppFilesystem::TMP_DIR,
        AppFilesystem::CACHE_DIR,
        AppFilesystem::LOG_DIR,
        AppFilesystem::SESSION_DIR,
    );

    /**
     * Codes of directories to create and verify in non-production mode
     *
     * @var array
     */
    protected static $nonProductionDirs = array(
        AppFilesystem::MEDIA_DIR,
        AppFilesystem::STATIC_VIEW_DIR,
        AppFilesystem::VAR_DIR,
        AppFilesystem::TMP_DIR,
        AppFilesystem::CACHE_DIR,
        AppFilesystem::LOG_DIR,
        AppFilesystem::SESSION_DIR,
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
     * @param Filesystem $filesystem
     * @param State $appState
     */
    public function __construct(Filesystem $filesystem, State $appState) {
        $this->filesystem = $filesystem;
        $this->dirsToVerify = $this->_getDirsToVerify($appState);
    }

    /**
     * Return list of directories, that must be verified according to the application mode
     *
     * @param State $appState
     * @return array
     */
    protected function _getDirsToVerify(State $appState)
    {
        $codes = ($appState->getMode() == State::MODE_PRODUCTION)
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
