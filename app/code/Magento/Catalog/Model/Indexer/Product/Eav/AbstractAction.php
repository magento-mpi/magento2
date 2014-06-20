<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Product\Eav;

/**
 * Abstract action reindex class
 */
abstract class AbstractAction
{
    /**
     * @var \Magento\Framework\Logger
     */
    protected $_logger;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Framework\Logger $logger
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\Logger $logger,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_logger = $logger;
        $this->_storeManager = $storeManager;
    }

    /**
     * Execute action for given ids
     *
     * @param array|int $ids
     * @return void
     */
    abstract public function execute($ids);
}
