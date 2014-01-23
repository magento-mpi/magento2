<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Install\App\Action\Plugin;

use Magento\App\Filesystem,
    Magento\Filesystem\FilesystemException,
    Magento\Filesystem\Directory\Write,
    Magento\App\State,
    Magento\Logger;

class Dir
{
    /**
     * Application state
     *
     * @var State
     */
    protected $appState;

    /**
     * Directory list
     *
     * @var Write
     */
    protected $varDirectory;

    /**
     * Logger
     *
     * @var Logger
     */
    protected $logger;

    /**
     * @param State $state
     * @param Filesystem $filesystem
     * @param Logger $logger
     */
    public function __construct(State $state, Filesystem $filesystem, Logger $logger)
    {
        $this->appState = $state;
        $this->varDirectory = $filesystem->getDirectoryWrite(Filesystem::VAR_DIR);
        $this->logger = $logger;
    }

    /**
     * Clear temporary directories
     *
     * @param $arguments
     * @return mixed
     */
    public function beforeDispatch($arguments)
    {
        if (!$this->appState->isInstalled()) {
            foreach ($this->varDirectory->read() as $dir) {
                if ($this->varDirectory->isDirectory($dir)) {
                    try {
                        $this->varDirectory->delete($dir);
                    } catch (FilesystemException $exception) {
                        $this->logger->log($exception->getMessage());
                    }
                }
            }
        }
        return $arguments;
    }
} 