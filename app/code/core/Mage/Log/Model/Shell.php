<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Log
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Shell model, used to work with logs via command line
 *
 * @category    Mage
 * @package     Mage_Log
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Log_Model_Shell extends Mage_Core_Model_Shell_Abstract
{
    /**
     * Log instance
     *
     * @var Mage_Log_Model_Log
     */
    protected $_log;

    /**
     * Retrieves Log instance
     *
     * @return Mage_Log_Model_Log
     */
    protected function _getLog()
    {
        if (is_null($this->_log)) {
            $this->_log = Mage::getModel('Mage_Log_Model_Log');
        }
        return $this->_log;
    }

    /**
     * Converts count to human view
     *
     * @param int $number
     * @return string
     */
    protected function _humanCount($number)
    {
        if ($number < 1000) {
            return $number;
        } else if ($number >= 1000 && $number < 1000000) {
            return sprintf('%.2fK', $number / 1000);
        } else if ($number >= 1000000 && $number < 1000000000) {
            return sprintf('%.2fM', $number / 1000000);
        } else {
            return sprintf('%.2fB', $number / 1000000000);
        }
    }

    /**
     * Converts size to human view
     *
     * @param int $number
     * @return string
     */
    protected function _humanSize($number)
    {
        if ($number < 1000) {
            return sprintf('%d b', $number);
        } else if ($number >= 1000 && $number < 1000000) {
            return sprintf('%.2fKb', $number / 1000);
        } else if ($number >= 1000000 && $number < 1000000000) {
            return sprintf('%.2fMb', $number / 1000000);
        } else {
            return sprintf('%.2fGb', $number / 1000000000);
        }
    }

    /**
     * Runs script
     *
     * @return Mage_Log_Model_Shell
     */
    public function run()
    {
        if ($this->_showHelp()) {
            return $this;
        }

        if ($this->getArg('clean')) {
            $days = $this->getArg('days');
            if ($days > 0) {
                Mage::app()->getStore()->setConfig(Mage_Log_Model_Log::XML_LOG_CLEAN_DAYS, $days);
            }
            $this->_getLog()->clean();
            echo "Log cleaned\n";
        } else if ($this->getArg('status')) {
            $resource = $this->_getLog()->getResource();
            $adapter  = $resource->getReadConnection();
            // log tables
            $tables = array(
                $resource->getTable('log_customer'),
                $resource->getTable('log_visitor'),
                $resource->getTable('log_visitor_info'),
                $resource->getTable('log_url_table'),
                $resource->getTable('log_url_info_table'),
                $resource->getTable('log_quote_table'),

                $resource->getTable('reports_viewed_product_index'),
                $resource->getTable('reports_compared_product_index'),
                $resource->getTable('reports_event'),

                $resource->getTable('catalog_compare_item'),
            );

            $rows        = 0;
            $dataLengh   = 0;
            $indexLength = 0;

            $line = '-----------------------------------+------------+------------+------------+' . "\n";
            echo $line;
            echo sprintf('%-35s|', 'Table Name');
            echo sprintf(' %-11s|', 'Rows');
            echo sprintf(' %-11s|', 'Data Size');
            echo sprintf(' %-11s|', 'Index Size');
            echo "\n";
            echo $line;

            foreach ($tables as $table) {
                $query  = $adapter->quoteInto('SHOW TABLE STATUS LIKE ?', $table);
                $status = $adapter->fetchRow($query);
                if (!$status) {
                    continue;
                }

                $rows += $status['Rows'];
                $dataLengh += $status['Data_length'];
                $indexLength += $status['Index_length'];

                echo sprintf('%-35s|', $table);
                echo sprintf(' %-11s|', $this->_humanCount($status['Rows']));
                echo sprintf(' %-11s|', $this->_humanSize($status['Data_length']));
                echo sprintf(' %-11s|', $this->_humanSize($status['Index_length']));
                echo "\n";
            }

            echo $line;
            echo sprintf('%-35s|', 'Total');
            echo sprintf(' %-11s|', $this->_humanCount($rows));
            echo sprintf(' %-11s|', $this->_humanSize($dataLengh));
            echo sprintf(' %-11s|', $this->_humanSize($indexLength));
            echo "\n";
            echo $line;
        } else {
            echo $this->getUsageHelp();
        }
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
        php -f {$this->_entryPoint} -- clean --days 1

  clean             Clean Logs
  --days <days>     Save log, days. (Minimum 1 day, if defined - ignoring system value)
  status            Display statistics per log tables
  help              This help

USAGE;
    }
}
