<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Indexer\App;

use Magento\Framework\App;
use Magento\Framework\App\Filesystem\DirectoryList;

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
        $directory = $this->filesystem->getDirectoryWrite(DirectoryList::ROOT);
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
