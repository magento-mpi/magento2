<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Indexer\App;

class Indexer implements \Magento\LauncherInterface
{
    /**
     * Report directory
     *
     * @var string
     */
    protected $reportDir;

    /**
     * @var \Magento\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\App\Console\Response
     */
    protected $response;

    /**
     * @param $reportDir
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\Indexer\Model\Processor $processor
     * @param \Magento\App\Console\Response $response
     */
    public function __construct(
        $reportDir,
        \Magento\Filesystem $filesystem,
        \Magento\Indexer\Model\Processor $processor,
        \Magento\App\Console\Response $response
    ) {
        $this->reportDir = $reportDir;
        $this->filesystem = $filesystem;
        $this->processor = $processor;
        $this->response = $response;
    }

    /**
     * Run application
     *
     * @return \Magento\App\ResponseInterface
     */
    public function launch()
    {
        /* Clean reports */
        $directory = $this->filesystem->getDirectoryWrite(\Magento\App\Filesystem::ROOT_DIR);
        $path = $directory->getRelativePath($this->reportDir);
        if ($directory->isExist($path)) {
            $directory->delete($path);
        }

        /* Regenerate all indexers */
        $this->processor->reindexAll();
        $this->response->setCode(0);

        return $this->response;
    }
}
