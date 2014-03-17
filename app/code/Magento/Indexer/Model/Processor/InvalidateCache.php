<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Indexer\Model\Processor;

/**
 * Class InvalidateCache
 */
class InvalidateCache
{
    /**
     * @var \Magento\Indexer\Model\CacheContext
     */
    protected $context;

    /**
     * @var \Magento\Event\Manager
     */
    protected $eventManager;

    /**
     * @var \Magento\Module\Manager
     */
    protected $moduleManager;

    /**
     * @param \Magento\Indexer\Model\CacheContext $context
     * @param \Magento\Event\Manager $eventManager
     * @param \Magento\Module\Manager $moduleManager
     */
    public function __construct(
        \Magento\Indexer\Model\CacheContext $context,
        \Magento\Event\Manager $eventManager,
        \Magento\Module\Manager $moduleManager
    ) {
        $this->context = $context;
        $this->eventManager = $eventManager;
        $this->moduleManager = $moduleManager;
    }

    /**
     * Update indexer views
     *
     * @param \Magento\Indexer\Model\Processor $subject
     * @param mixed $result
     * @return mixed
     */
    public function afterUpdateMview(\Magento\Indexer\Model\Processor $subject, $result)
    {
        if ($this->moduleManager->isEnabled('Magento_PageCache')) {
            $this->eventManager->dispatch('clean_cache_after_reindex', array('object' => $this->context));
        }
        return $result;
    }
}
