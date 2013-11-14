<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Block\Cache;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;

/**
 * Class Actions
 * Cache actions block
 *
 * @package Magento\Backend\Test\Block\Cache
 */
class Actions extends Block
{
    /**
     * Flush cache button selector
     *
     * @var string
     */
    private $flushMagentoCacheButton;

    /**
     * Flush Cache Storage button selector
     *
     * @var string
     */
    private $flushCacheStorageButton;

    /**
     * Selector for messages block
     *
     * @var string
     */
    protected $messagesSelector = '//ancestor::div//div[@id="messages"]';

    /**
     * Messages texts
     *
     * @var array
     */
    protected $messagesText = array(
        'cache_storage_flushed' => 'You flushed the cache storage.',
        'cache_magento_flushed' => 'The Magento cache storage has been flushed.',
    );

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        $this->flushMagentoCacheButton = '#flush_magento';
        $this->flushCacheStorageButton = '#flush_system';
    }

    /**
     * Flush magento cache
     */
    public function flushMagentoCache()
    {
        $this->_rootElement->find($this->flushMagentoCacheButton)->click();
    }

    /**
     * Flush cache storage
     */
    public function flushCacheStorage()
    {
        $this->_rootElement->find($this->flushCacheStorageButton)->click();
        $this->_rootElement->acceptAlert();
    }

    /**
     * Is storage cache flushed successfully
     *
     * @return bool
     */
    public function isStorageCacheFlushed()
    {
        return $this->getMessageBlock()->getSuccessMessages() == $this->messagesText['cache_storage_flushed'];
    }

    /**
     * Is magento cache flushed successfully
     *
     * @return bool
     */
    public function isMagentoCacheFlushed()
    {
        return $this->getMessageBlock()->getSuccessMessages() == $this->messagesText['cache_magento_flushed'];
    }

    /**
     * Get messages block
     *
     * @return \Magento\Core\Test\Block\Messages
     */
    protected function getMessageBlock()
    {
        return Factory::getBlockFactory()->getMagentoCoreMessages(
            $this->_rootElement->find($this->messagesSelector, Locator::SELECTOR_XPATH)
        );
    }
}
