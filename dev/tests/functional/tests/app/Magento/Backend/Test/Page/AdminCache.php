<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Page;

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Class AdminCache
 * Cache Management page
 *
 */
class AdminCache extends Page
{
    /**
     * URL part for cache management page
     */
    const MCA = 'admin/cache/';

    /**
     * Cache actions block
     *
     * @var string
     */
    protected $cacheBlock = 'div.page-actions';

    /**
     * Global messages block
     *
     * @var string
     */
    protected $messagesBlock = '#messages .messages';

    /**
     * Constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
    }

    /**
     * Get cache actions block
     *
     * @return \Magento\Backend\Test\Block\Cache
     */
    public function getActionsBlock()
    {
        return Factory::getBlockFactory()->getMagentoBackendCache(
            $this->_browser->find($this->cacheBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get messages block
     *
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return Factory::getBlockFactory()->getMagentoCoreMessages(
            $this->_browser->find($this->messagesBlock, Locator::SELECTOR_CSS)
        );
    }
}
