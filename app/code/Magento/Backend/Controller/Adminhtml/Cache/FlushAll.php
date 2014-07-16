<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\Cache;

class FlushAll extends \Magento\Backend\Controller\Adminhtml\Cache
{
    /**
     * Flush cache storage
     *
     * @return void
     */
    public function execute()
    {
        $this->_eventManager->dispatch('adminhtml_cache_flush_all');
        /** @var $cacheFrontend \Magento\Framework\Cache\FrontendInterface */
        foreach ($this->_cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->getBackend()->clean();
        }
        $this->messageManager->addSuccess(__("You flushed the cache storage."));
        $this->_redirect('adminhtml/*');
    }
}
