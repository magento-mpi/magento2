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
    }
}
