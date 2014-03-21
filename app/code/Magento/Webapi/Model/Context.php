<?php
/**
 * Abstract model context
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Webapi\Model;

class Context extends \Magento\Model\Context
{
    /**
     * @param \Magento\Logger $logger
     * @param \Magento\Event\ManagerInterface $eventDispatcher
     * @param \Magento\App\CacheInterface $cacheManager
     * @param \Magento\App\State $appState
     * @param \Magento\Model\RemoveProtector\Disabled $removeProtector
     */
    public function __construct(
        \Magento\Logger $logger,
        \Magento\Event\ManagerInterface $eventDispatcher,
        \Magento\App\CacheInterface $cacheManager,
        \Magento\App\State $appState,
        \Magento\Model\RemoveProtector\Disabled $removeProtector
    ) {
        parent::__construct($logger, $eventDispatcher, $cacheManager, $appState, $removeProtector);
    }
}
