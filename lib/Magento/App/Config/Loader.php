<?php
/**
 * Loader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App\Config;

class Loader implements \Magento\App\Config\LoaderInterface
{
    /**
     * @var \Magento\App\Config\Scope\Resolver
     */
    protected $_scopeResolver;

    /**
     * @var \Magento\App\Config\ScopePool
     */
    protected $_scopePool;

    /**
     * @param \Magento\App\Config\Scope\Resolver $scopeResolver
     * @param \Magento\App\Config\ScopePool $scopePool
     */
    public function __construct(
        \Magento\App\Config\Scope\Resolver $scopeResolver,
        \Magento\App\Config\ScopePool $scopePool
    ) {
        $this->_scopeResolver = $scopeResolver;
        $this->_scopePool = $scopePool;
    }

    /**
     * Process of config loading
     *
     * @return \Magento\App\Config\DataInterface
     */
    public function load()
    {
        return $this->_scopePool->getScope($this->_scopeResolver->getScope());
    }
}
