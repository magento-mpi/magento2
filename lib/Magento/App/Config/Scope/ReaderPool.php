<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\App\Config\Scope;

class ReaderPool
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
