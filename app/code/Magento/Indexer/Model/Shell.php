<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Indexer\Model;

class Shell extends \Magento\Core\Model\AbstractShell
{
    /**
     * Error status - whether errors have happened
     *
     * @var bool
     */
    protected $hasErrors = false;

    /**
     * @var Indexer\CollectionFactory
     */
    protected $indexersFactory;

    /**
     * @var IndexerFactory
     */
    protected $indexerFactory;

    /**
     * @param \Magento\Filesystem $filesystem
     * @param string $entryPoint
     * @param Indexer\CollectionFactory $indexersFactory
     * @param IndexerFactory $indexerFactory
     */
    public function __construct(
        \Magento\Filesystem $filesystem,
        $entryPoint,
        Indexer\CollectionFactory $indexersFactory,
        IndexerFactory $indexerFactory
    ) {
        $this->indexersFactory = $indexersFactory;
        $this->indexerFactory = $indexerFactory;
        parent::__construct($filesystem, $entryPoint);
    }

    /**
     * Run this model, assumed to be run by command-line
     *
     * @return \Magento\Indexer\Model\Shell
     */
    public function run()
    {
        if ($this->_showHelp()) {
            return $this;
        }

        if ($this->getArg('info')) {
            $this->runShowInfo();
        } else if ($this->getArg('status') || $this->getArg('mode')) {
            $this->runShowStatusOrMode();
        } else if ($this->getArg('mode-realtime') || $this->getArg('mode-schedule')) {
            $this->runSetMode();
        } else if ($this->getArg('reindex') || $this->getArg('reindexall')) {
            $this->runReindex();
        } else {
            echo $this->getUsageHelp();
        }

        return $this;
    }

    /**
     * Show information about indexes
     *
     * @return \Magento\Indexer\Model\Shell
     */
    protected function runShowInfo()
    {
        $indexers = $this->parseIndexerString('all');
        foreach ($indexers as $indexer) {
            echo sprintf('%-40s', $indexer->getId());
            echo $indexer->getTitle() . "\n";
        }

        return $this;
    }

    /**
     * Show information about statuses or modes
     *
     * @return \Magento\Indexer\Model\Shell
     */
    protected function runShowStatusOrMode()
    {
        if ($this->getArg('status')) {
            $indexers = $this->parseIndexerString($this->getArg('status'));
        } else {
            $indexers = $this->parseIndexerString($this->getArg('mode'));
        }

        foreach ($indexers as $indexer) {
            $status = 'unknown';
            if ($this->getArg('status')) {
                switch ($indexer->getStatus()) {
                    case \Magento\Indexer\Model\Indexer\State::STATUS_VALID:
                        $status = 'Ready';
                        break;
                    case \Magento\Indexer\Model\Indexer\State::STATUS_INVALID:
                        $status = 'Reindex required';
                        break;

                    case \Magento\Indexer\Model\Indexer\State::STATUS_WORKING:
                        $status = 'Processing';
                        break;
                }
            } else {
                switch ($indexer->getMode()) {
                    case \Magento\Mview\View\StateInterface::MODE_DISABLED:
                        $status = 'Update on Save';
                        break;
                    case \Magento\Mview\View\StateInterface::MODE_ENABLED:
                        $status = 'Update by Schedule';
                        break;
                }
            }
            echo sprintf('%-50s ', $indexer->getTitle() . ':') . $status ."\n";
        }

        return $this;
    }

    /**
     * Set new mode for indexers
     *
     * @return \Magento\Indexer\Model\Shell
     */
    protected function runSetMode()
    {
        if ($this->getArg('mode-realtime')) {
            $method = 'turnViewOff';
            $indexers = $this->parseIndexerString($this->getArg('mode-realtime'));
        } else {
            $method = 'turnViewOn';
            $indexers = $this->parseIndexerString($this->getArg('mode-schedule'));
        }

        foreach ($indexers as $indexer) {
            try {
                $indexer->$method();
                echo $indexer->getTitle() . " indexer was successfully changed index mode\n";
            } catch (\Magento\Core\Exception $e) {
                echo $e->getMessage() . "\n";
                $this->hasErrors = true;
            } catch (\Exception $e) {
                echo $indexer->getTitle() . " indexer process unknown error:\n";
                echo $e . "\n";
                $this->hasErrors = true;
            }
        }

        return $this;
    }

    /**
     * Reindex indexer(s)
     *
     * @return \Magento\Indexer\Model\Shell
     */
    protected function runReindex()
    {
        if ($this->getArg('reindex')) {
            $indexers = $this->parseIndexerString($this->getArg('reindex'));
        } else {
            $indexers = $this->parseIndexerString('all');
        }

        $isGroupBuilt = array();
        foreach ($indexers as $indexer) {
            try {
                if (!$indexer->getGroup() || !isset($isGroupBuilt[$indexer->getGroup()])
                    || !$isGroupBuilt[$indexer->getGroup()]
                ) {
                    $indexer->reindexAll();
                    if ($indexer->getGroup()) {
                        $isGroupBuilt[$indexer->getGroup()] = true;
                    }
                }
                echo $indexer->getTitle() . " index has been rebuilt successfully\n";
            } catch (\Magento\Core\Exception $e) {
                echo $e->getMessage() . "\n";
                $this->hasErrors = true;
            } catch (\Exception $e) {
                echo $indexer->getTitle() . " indexer process unknown error:\n";
                echo $e . "\n";
                $this->hasErrors = true;
            }
        }

        return $this;
    }

    /**
     * Parses string with indexers and return array of indexer instances
     *
     * @param string $string
     * @return Indexer[]
     */
    protected function parseIndexerString($string)
    {
        $indexers = array();
        if ($string == 'all') {
            /** @var Indexer[] $indexers */
            $indexers = $this->indexersFactory->create()->getItems();
        } else if (!empty($string)) {
            $codes = explode(',', $string);
            foreach ($codes as $code) {
                $indexer = $this->indexerFactory->create();
                try {
                    $indexer->load($code);
                    $indexers[] = $indexer;
                } catch (\Exception $e) {
                    echo 'Warning: Unknown indexer with code ' . trim($code) . "\n";
                    $this->hasErrors = true;
                }
            }
        }
        return $indexers;
    }

    /**
     * Return whether there errors have happened
     *
     * @return bool
     */
    public function hasErrors()
    {
        return $this->hasErrors;
    }

    /**
     * Retrieves usage help message
     *
     * @return string
     */
    public function getUsageHelp()
    {
        return <<<USAGE
Usage:  php -f {$this->_entryPoint} -- [options]

  --status <indexer>            Show Indexer(s) Status
  --mode <indexer>              Show Indexer(s) Index Mode
  --mode-realtime <indexer>     Set index mode type "Update on Save"
  --mode-schedule <indexer>     Set index mode type "Update by Schedule"
  --reindex <indexer>           Reindex Data
  info                          Show allowed indexers
  reindexall                    Reindex Data by all indexers
  help                          This help

  <indexer>     Comma separated indexer codes or value "all" for all indexers
USAGE;
    }
}
