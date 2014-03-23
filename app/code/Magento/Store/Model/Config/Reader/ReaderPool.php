<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Store\Model\Config\Reader;

class ReaderPool implements \Magento\App\Config\Scope\ReaderPoolInterface
{
    /**
     * List of readers
     *
     * @var array
     */
    protected $_readers = array();

    /**
     * @param ReaderInterface $default
     * @param ReaderInterface $website
     * @param ReaderInterface $store
     */
    public function __construct(
        \Magento\App\Config\Scope\ReaderInterface $default,
        \Magento\App\Config\Scope\ReaderInterface $website,
        \Magento\App\Config\Scope\ReaderInterface $store
    ) {
        $this->_readers = array(
            \Magento\BaseScopeInterface::SCOPE_DEFAULT => $default,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE => $website,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES => $website,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE => $store,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORES => $store
        );
    }

    /**
     * Retrieve reader by scope type
     *
     * @param string $scopeType
     * @return mixed
     */
    public function getReader($scopeType)
    {
        return $this->_readers[$scopeType];
    }
} 
