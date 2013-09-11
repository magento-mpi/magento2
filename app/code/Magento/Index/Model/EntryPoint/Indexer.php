<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Index\Model\EntryPoint;

class Indexer extends \Magento\Core\Model\EntryPointAbstract
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
     * @param string $reportDir absolute path to report directory to be cleaned
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\Core\Model\Config\Primary $config
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(
        $reportDir,
        \Magento\Filesystem $filesystem,
        \Magento\Core\Model\Config\Primary $config,
        \Magento\ObjectManager $objectManager = null
    ) {
        parent::__construct($config, $objectManager);
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
