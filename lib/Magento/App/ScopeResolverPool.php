<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App;

class ScopeResolverPool
{
    /**
     * @var array
     */
    protected $_scopeResolvers = array();

    /**
     * @param \Magento\BaseScopeResolverInterface[] $scopeResolvers
     */
    public function __construct(
        array $scopeResolvers
    ) {
        $this->_scopeResolvers = $scopeResolvers;
    }

    /**
     * Retrieve reader by scope type
     *
     * @param string $scopeType
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function get($scopeType)
    {
        if (!isset($this->_scopeResolvers[$scopeType]) ||
            !($this->_scopeResolvers[$scopeType] instanceof \Magento\BaseScopeResolverInterface)
        ) {
            throw new \InvalidArgumentException("Invalid scope type '{$scopeType}'");
        }
        return $this->_scopeResolvers[$scopeType];
    }
}
