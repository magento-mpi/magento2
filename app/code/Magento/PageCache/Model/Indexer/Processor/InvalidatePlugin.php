<?php
/**
 * @category   Magento
 * @package    Magento_PageCache
 * @author     Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\PageCache\Model\Indexer\Processor;

/**
 * Class InvalidatePlugin
 */
class InvalidatePlugin
{
    /**
     * @var \Magento\PageCache\Model\Indexer\Context
     */
    protected $context;

    /**
     * @var \Magento\Event\Manager
     */
    protected $eventManager;

    /**
     * @param \Magento\PageCache\Model\Indexer\Context $context
     * @param \Magento\Event\Manager $eventManager
     */
    public function __construct(
        \Magento\PageCache\Model\Indexer\Context $context,
        \Magento\Event\Manager $eventManager
    ) {
        $this->context = $context;
        $this->eventManager = $eventManager;
    }

    /**
     * Update indexer views
     *
     * @param \Magento\Indexer\Model\Processor $subject
     * @param $result
     */
    public function afterUpdateMview(\Magento\Indexer\Model\Processor $subject, $result)
    {
        $this->eventManager->dispatch('clean_cache_after_reindex', array('object' => $this->context));
        return $result;
    }
}
