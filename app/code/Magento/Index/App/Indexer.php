<?php
/**
 * Indexer application
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Index\App;

use Magento\Framework\App\Console\Response;
use Magento\Framework\App;
use Magento\Framework\AppInterface;

class Indexer implements AppInterface
{
    /**
     * Report directory
     *
     * @var string
     */
    protected $_reportDir;

    /**
     * @var \Magento\Framework\App\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Index\Model\IndexerFactory
     */
    protected $_indexerFactory;

    /**
     * @var \Magento\Framework\App\Console\Response
     */
    protected $_response;

    /**
     * @param string $reportDir
     * @param \Magento\Framework\App\Filesystem $filesystem
     * @param \Magento\Index\Model\IndexerFactory $indexerFactory
     * @param Response $response
     */
    public function __construct(
        $reportDir,
        \Magento\Framework\App\Filesystem $filesystem,
        \Magento\Index\Model\IndexerFactory $indexerFactory,
        Response $response
    ) {
        $this->_reportDir = $reportDir;
        $this->_filesystem = $filesystem;
        $this->_indexerFactory = $indexerFactory;
        $this->_response = $response;
    }

    /**
     * Run application
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function launch()
    {
        /* Clean reports */
        $directory = $this->_filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem::ROOT_DIR);
        $path = $directory->getRelativePath($this->_reportDir);
        if ($directory->isExist($path)) {
            $directory->delete($path);
        }

        /* Run all indexer processes */
        /** @var $indexer \Magento\Index\Model\Indexer */
        $indexer = $this->_indexerFactory->create();
        /** @var $process \Magento\Index\Model\Process */
        foreach ($indexer->getProcessesCollection() as $process) {
            if ($process->getIndexer()->isVisible()) {
                $process->reindexEverything();
            }
        }
        $this->_response->setCode(0);
        return $this->_response;
    }

    /**
     * {@inheritdoc}
     */
    public function catchException(App\Bootstrap $bootstrap, \Exception $exception)
    {
        return false;
    }
}
