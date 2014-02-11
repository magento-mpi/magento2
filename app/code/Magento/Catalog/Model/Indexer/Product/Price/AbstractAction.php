<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Product\Price;

/**
 * Abstract action reindex class
 *
 * @package Magento\Catalog\Model\Indexer\Product\Price
 */
abstract class AbstractAction
{
    /**
     * Logger instance
     *
     * @var \Magento\Logger
     */
    protected $_logger;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Logger $logger
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Logger $logger,
        \Magento\Core\Model\StoreManagerInterface $storeManager
    ) {
        $this->_logger = $logger;
        $this->_storeManager = $storeManager;
    }

    /**
     * Execute action for given ids
     *
     * @param array|int $ids
     * @return mixed
     */
    abstract public function execute($ids);

}
