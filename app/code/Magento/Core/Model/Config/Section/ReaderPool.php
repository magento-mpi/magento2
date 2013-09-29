<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Core\Model\Config\Section;

class ReaderPool
{
    /**
     * List of readers
     *
     * @var array
     */
    protected $_readers = array();

    /**
     * @param \Magento\Core\Model\Config\Section\Reader\DefaultReader $default
     * @param \Magento\Core\Model\Config\Section\Reader\Website $website
     * @param \Magento\Core\Model\Config\Section\Reader\Store $store
     */
    public function __construct(
        \Magento\Core\Model\Config\Section\Reader\DefaultReader $default,
        \Magento\Core\Model\Config\Section\Reader\Website $website,
        \Magento\Core\Model\Config\Section\Reader\Store $store
    ) {
        $this->_readers = array(
            'default' => $default,
            'website' => $website,
            'websites' => $website,
            'store' => $store,
            'stores' => $store
        );
    }

    /**
     * Retrieve reader by scope
     *
     * @param string $scope
     * @return mixed
     */
    public function getReader($scope)
    {
        return $this->_readers[$scope];
    }
} 
