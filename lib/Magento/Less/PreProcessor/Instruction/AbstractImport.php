<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Less\PreProcessor\Instruction;

use Magento\Less\PreProcessorInterface;

/**
 * Abstract instruction preprocessor for all import instructions
 */
abstract class AbstractImport implements PreProcessorInterface
{
    /**
     * Import's path list where key is relative path and value is absolute path to the imported content
     *
     * @var array
     */
    protected $importPaths = [];

    /**
     * @var \Magento\Less\PreProcessor
     */
    protected $preProcessor;

    /**
     * @var \Magento\Logger
     */
    protected $logger;

    /**
     * @var array
     */
    protected $viewParams;

    /**
     * @param \Magento\Less\PreProcessor $preProcessor
     * @param \Magento\Logger $logger
     * @param array $viewParams
     */
    public function __construct(
        \Magento\Less\PreProcessor $preProcessor,
        \Magento\Logger $logger,
        array $viewParams = array()
    ) {
        $this->preProcessor = $preProcessor;
        $this->logger = $logger;
        $this->viewParams = $viewParams;
    }

    /**
     * Explode import paths
     *
     * @param array $importPaths
     * @return $this
     */
    protected function generatePaths($importPaths)
    {
        foreach ($importPaths as $path) {
            $path = $this->preparePath($path);
            try {
                $this->importPaths[$path] = $this->preProcessor->processLessInstructions($path, $this->viewParams);
            } catch (\Magento\Filesystem\FilesystemException $e) {
                $this->logger->logException($e);
            }
        }
        return $this;
    }

    /**
     * Prepare relative path to less compatible state
     *
     * @param string $lessSourcePath
     * @return string
     */
    protected function preparePath($lessSourcePath)
    {
        return pathinfo($lessSourcePath, PATHINFO_EXTENSION) ? $lessSourcePath : $lessSourcePath . '.less';
    }
}
