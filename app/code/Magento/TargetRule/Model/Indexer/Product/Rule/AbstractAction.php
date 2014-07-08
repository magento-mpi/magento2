<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Model\Indexer\Product\Rule;

/**
 * Abstract action reindex class
 *
 * @package Magento\TargetRule\Model\Indexer\Product\Rule
 */
abstract class AbstractAction
{
    /**
     * Logger instance
     *
     * @var  \Magento\Framework\Logger
     */
    protected $_logger;

    /**
     * @param \Magento\Framework\Logger $logger
     */
    public function __construct(
        \Magento\Framework\Logger $logger
    ) {
        /**
         * @TODO delete logger after finishing indexer implementation
         */
        $this->_logger = $logger;
    }

    /**
     * Execute action for given ids
     *
     * @param array|int $ids
     *
     * @return void
     */
    abstract public function execute($ids);

    /**
     * Refresh entities index
     *
     * @param array $productIds
     * @return array Affected ids
     */
    protected function _reindexRows($productIds = array())
    {
        /**
         * @TODO delete logger after finishing indexer implementation
         */
        $this->_logger->log('Rows reindex for products - ' . implode(",", $productIds) . '');
    }

    /**
     * Reindex all
     *
     * @return void
     */
    public function reindexAll()
    {
        /**
         * @TODO delete logger after finishing indexer implementation
         */
        $this->_logger->log('Full reindex');
    }
}
