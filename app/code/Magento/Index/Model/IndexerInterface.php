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
 * Indexer interface
 */
interface IndexerInterface
{
    /**
     * Get indexer name
     *
     * @return mixed
     */
    public function getName();

    /**
     * Get Indexer description
     *
     * @return string
     */
    public function getDescription();

    /**
     * Register data required by process in event object
     *
     * @param \Magento\Index\Model\Event $event
     * @return \Magento\Index\Model\IndexerInterface
     */
    public function register(\Magento\Index\Model\Event $event);

    /**
     * Process event
     *
     * @param \Magento\Index\Model\Event $event
     * @return \Magento\Index\Model\IndexerInterface
     */
    public function processEvent(\Magento\Index\Model\Event $event);

    /**
     * Check if event can be matched by process
     *
     * @param \Magento\Index\Model\Event $event
     * @return bool
     */
    public function matchEvent(\Magento\Index\Model\Event $event);

    /**
     * Check if indexer matched specific entity and action type
     *
     * @param   string $entity
     * @param   string $type
     * @return  bool
     */
    public function matchEntityAndType($entity, $type);

    /**
     * Rebuild all index data
     */
    public function reindexAll();

    /**
     * Try dynamicly detect and call event hanler from resource model.
     * Handler name will be generated from event entity and type code
     *
     * @param   \Magento\Index\Model\Event $event
     * @return  \Magento\Index\Model\Indexer\AbstractIndexer
     */
    public function callEventHandler(\Magento\Index\Model\Event $event);

    /**
     * Whether the indexer should be displayed on process/list page
     *
     * @return bool
     */
    public function isVisible();
}
