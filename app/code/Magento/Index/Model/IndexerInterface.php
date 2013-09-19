<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Index
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Indexer interface
 */
interface Magento_Index_Model_IndexerInterface
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
     * @param Magento_Index_Model_Event $event
     * @return Magento_Index_Model_IndexerInterface
     */
    public function register(Magento_Index_Model_Event $event);

    /**
     * Process event
     *
     * @param Magento_Index_Model_Event $event
     * @return Magento_Index_Model_IndexerInterface
     */
    public function processEvent(Magento_Index_Model_Event $event);

    /**
     * Check if event can be matched by process
     *
     * @param Magento_Index_Model_Event $event
     * @return bool
     */
    public function matchEvent(Magento_Index_Model_Event $event);

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
     * @param   Magento_Index_Model_Event $event
     * @return  Magento_Index_Model_Indexer_Abstract
     */
    public function callEventHandler(Magento_Index_Model_Event $event);

    /**
     * Whether the indexer should be displayed on process/list page
     *
     * @return bool
     */
    public function isVisible();
}