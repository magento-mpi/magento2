<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Indexer\App;

use Magento\Framework\App;

class Indexer implements \Magento\Framework\AppInterface
{
    /**
     * Report directory
     *
     * @var string
     */
    protected $reportDir;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Index\Model\IndexerFactory
     */
    protected $_indexerFactory;

    /**
     * @param string $reportDir
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Indexer\Model\Processor $processor
     */
    public function __construct(
        $reportDir,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Indexer\Model\Processor $processor
    ) {
        $this->reportDir = $reportDir;
        $this->filesystem = $filesystem;
        $this->processor = $processor;
    }

    /**
     * Run application
     *
     * @return int
     */
    public function launch()
    {
        /* Clean reports */
        $directory = $this->filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem::ROOT_DIR);
        $path = $directory->getRelativePath($this->reportDir);
        if ($directory->isExist($path)) {
            $directory->delete($path);
        }

        /* Regenerate all indexers */
        $this->processor->reindexAll();

        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function catchException(App\Bootstrap $bootstrap, \Exception $exception)
    {
        return false;
    }
}
