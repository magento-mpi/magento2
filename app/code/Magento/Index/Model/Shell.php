<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Index
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Index\Model;

/**
 * Shell model, used to work with indexers via command line
 *
 * @category    Magento
 * @package     Magento_Index
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shell extends \Magento\Framework\App\AbstractShell
{
    /**
     * Error status - whether errors have happened
     *
     * @var bool
     */
    protected $_hasErrors = false;

    /**
     * @var \Magento\Index\Model\Indexer
     */
    protected $_indexer;

    /**
     * @param \Magento\Framework\App\Filesystem $filesystem
     * @param string $entryPoint
     * @param Indexer $indexer
     */
    public function __construct(\Magento\Framework\App\Filesystem $filesystem, $entryPoint, Indexer $indexer)
    {
        $this->_indexer = $indexer;
        parent::__construct($filesystem, $entryPoint);
    }

    /**
     * Runs this model, assumed to be run by command-line
     *
     * @return $this
     */
    public function run()
    {
        if ($this->_showHelp()) {
            return $this;
        }

        if ($this->getArg('info')) {
            $this->_runShowInfo();
        } else if ($this->getArg('status') || $this->getArg('mode')) {
            $this->_runShowStatusOrMode();
        } else if ($this->getArg('mode-realtime') || $this->getArg('mode-manual')) {
            $this->_runSetMode();
        } else if ($this->getArg('reindex') || $this->getArg('reindexall')) {
            $this->_runReindex();
        } else {
            echo $this->getUsageHelp();
        }
        return $this;
    }

    /**
     * Shows information about indexes
     *
     * @return $this
     */
    protected function _runShowInfo()
    {
        $processes = $this->_parseIndexerString('all');
        foreach ($processes as $process) {
            /* @var $process \Magento\Index\Model\Process */
            echo sprintf('%-30s', $process->getIndexerCode());
            echo $process->getIndexer()->getName() . "\n";
        }
        return $this;
    }

    /**
     * Shows information about statuses or modes
     *
     * @return $this
     */
    protected function _runShowStatusOrMode()
    {
        if ($this->getArg('status')) {
            $processes = $this->_parseIndexerString($this->getArg('status'));
        } else {
            $processes = $this->_parseIndexerString($this->getArg('mode'));
        }
        foreach ($processes as $process) {
            /* @var $process \Magento\Index\Model\Process */
            $status = 'unknown';
            if ($this->getArg('status')) {
                switch ($process->getStatus()) {
                    case \Magento\Index\Model\Process::STATUS_PENDING:
                        $status = 'Pending';
                        break;
                    case \Magento\Index\Model\Process::STATUS_REQUIRE_REINDEX:
                        $status = 'Require Reindex';
                        break;

                    case \Magento\Index\Model\Process::STATUS_RUNNING:
                        $status = 'Running';
                        break;

                    default:
                        $status = 'Ready';
                        break;
                }
            } else {
                switch ($process->getMode()) {
                    case \Magento\Index\Model\Process::MODE_REAL_TIME:
                        $status = 'Update on Save';
                        break;
                    case \Magento\Index\Model\Process::MODE_MANUAL:
                        $status = 'Manual Update';
                        break;
                }
            }
            echo sprintf('%-30s ', $process->getIndexer()->getName() . ':') . $status . "\n";
        }
        return $this;
    }

    /**
     * Sets new mode for indexes
     *
     * @return $this
     */
    protected function _runSetMode()
    {
        if ($this->getArg('mode-realtime')) {
            $mode = \Magento\Index\Model\Process::MODE_REAL_TIME;
            $processes = $this->_parseIndexerString($this->getArg('mode-realtime'));
        } else {
            $mode = \Magento\Index\Model\Process::MODE_MANUAL;
            $processes = $this->_parseIndexerString($this->getArg('mode-manual'));
        }
        foreach ($processes as $process) {
            /* @var $process \Magento\Index\Model\Process */
            try {
                $process->setMode($mode)->save();
                echo $process->getIndexer()->getName() . " index was successfully changed index mode\n";
            } catch (\Magento\Model\Exception $e) {
                echo $e->getMessage() . "\n";
                $this->_hasErrors = true;
            } catch (\Exception $e) {
                echo $process->getIndexer()->getName() . " index process unknown error:\n";
                echo $e . "\n";
                $this->_hasErrors = true;
            }
        }
        return $this;
    }

    /**
     * Reindexes indexer(s)
     *
     * @return void
     */
    protected function _runReindex()
    {
        if ($this->getArg('reindex')) {
            $processes = $this->_parseIndexerString($this->getArg('reindex'));
        } else {
            $processes = $this->_parseIndexerString('all');
        }

        foreach ($processes as $process) {
            /* @var $process \Magento\Index\Model\Process */
            try {
                $startTime = microtime(true);
                $process->reindexEverything();
                $resultTime = microtime(true) - $startTime;
                echo $process->getIndexer()->getName()
                    . " index was rebuilt successfully in " . gmdate('H:i:s', $resultTime) . "\n";
            } catch (\Magento\Model\Exception $e) {
                echo $e->getMessage() . "\n";
                $this->_hasErrors = true;
            } catch (\Exception $e) {
                echo $process->getIndexer()->getName() . " index process unknown error:\n";
                echo $e . "\n";
                $this->_hasErrors = true;
            }
        }
    }

    /**
     * Parses string with indexers and return array of indexer instances
     *
     * @param string $string
     * @return array
     */
    protected function _parseIndexerString($string)
    {
        $processes = array();
        if ($string == 'all') {
            $collection = $this->_indexer->getProcessesCollection();
            foreach ($collection as $process) {
                $processes[] = $process;
            }
        } else if (!empty($string)) {
            $codes = explode(',', $string);
            foreach ($codes as $code) {
                $process = $this->_indexer->getProcessByCode(trim($code));
                if (!$process) {
                    echo 'Warning: Unknown indexer with code ' . trim($code) . "\n";
                    $this->_hasErrors = true;
                } else {
                    $processes[] = $process;
                }
            }
        }
        return $processes;
    }

    /**
     * Return whether there errors have happened
     *
     * @return bool
     */
    public function hasErrors()
    {
        return $this->_hasErrors;
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
  --mode-manual <indexer>       Set index mode type "Manual Update"
  --reindex <indexer>           Reindex Data
  info                          Show allowed indexers
  reindexall                    Reindex Data by all indexers
  help                          This help

  <indexer>     Comma separated indexer codes or value "all" for all indexers
USAGE;
    }
}
