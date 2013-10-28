<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Index\Model\EntryPoint;

class Indexer extends \Magento\App\AbstractEntryPoint
{
    /**
     * Report directory
     *
     * @var string
     */
    protected $_reportDir;

    /**
     * @var \Magento\Filesystem
     */
    protected $_filesystem;

    /**
     * @param string $baseDir
     * @param array $parameters
     * @param \Magento\ObjectManager\ObjectManager $reportDir
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(
        $baseDir,
        $parameters,
        $reportDir,
        \Magento\Filesystem $filesystem,
        \Magento\ObjectManager $objectManager = null
    ) {
        parent::__construct($baseDir, $parameters, $objectManager);
        $this->_reportDir = $reportDir;
        $this->_filesystem = $filesystem;
    }

    /**
     * Process request to application
     */
    protected function _processRequest()
    {
        /* Clean reports */
        $this->_filesystem->delete($this->_reportDir, dirname($this->_reportDir));

        /* Run all indexer processes */
        /** @var $indexer \Magento\Index\Model\Indexer */
        $indexer = $this->_objectManager->create('Magento\Index\Model\Indexer');
        /** @var $process \Magento\Index\Model\Process */
        foreach ($indexer->getProcessesCollection() as $process) {
            if ($process->getIndexer()->isVisible()) {
                $process->reindexEverything();
            }
        }
    }
}
