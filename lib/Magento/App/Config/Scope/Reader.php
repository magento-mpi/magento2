<?php
/**
 * Scope Reader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Config\Scope;

class Reader
{
    /**
     * @var \Magento\App\Config\Initial
     */
    protected $_initialConfig;

    /**
     * @var \Magento\App\Config\ScopePool
     */
    protected $_sectionPool;

    /**
     * @var \Magento\Config\ConverterInterface
     */
    protected $_converter;

    /**
     * @var \Magento\Config\ProcessorInterface
     */
    protected $_processor;

    /**
     * @var \Magento\App\Config\Scope\FactoryInterface
     */
    protected $_scopeFactory;

    /**
     * @var \Magento\App\Config\Scope\HierarchyInterface
     */
    protected $_scopeHierarchy;

    /**
     *
     * @param \Magento\App\Config\Initial $initialConfig
     * @param \Magento\Config\ConverterInterface $converter
     * @param \Magento\Config\Data\ProcessorInterface $processor
     * @param \Magento\App\Config\Scope\FactoryInterface $scopeFactory
     * @param \Magento\App\Config\Scope\HierarchyInterface $scopeHierarchy
     */
    public function __construct(
        \Magento\App\Config\Initial $initialConfig,
        \Magento\Config\ConverterInterface $converter,
        \Magento\App\Config\Data\ProcessorInterface $processor,
        \Magento\App\Config\Scope\FactoryInterface $scopeFactory,
        \Magento\App\Config\Scope\HierarchyInterface $scopeHierarchy
    ) {
        $this->_initialConfig = $initialConfig;
        $this->_converter = $converter;
        $this->_processor = $processor;
        $this->_scopeFactory = $scopeFactory;
        $this->_scopeHierarchy = $scopeHierarchy;
    }

    public function read($scope)
    {
        $config = array();
        $scopes = $this->_scopeHierarchy->getHierarchy($scope);
        foreach ($scopes as $scope) {
            $config = array_replace_recursive($config, $this->_getInitialConfigData($scope));
            $config = array_replace_recursive($config, $this->_getExtendedConfigData($scope));
        }
        return $this->_processor->process($config);
    }

    /**
     * Retrieve initial scope config from xml files
     *
     * @param string $scope
     * @return array
     */
    protected function _getInitialConfigData($scope)
    {
        return $this->_initialConfig->getData($scope);
    }

    /**
     * Retrieve scope config from database
     *
     * @param string $scope
     * @return array
     */
    protected function _getExtendedConfigData($scope)
    {
        list($scopeType, $scopeCode) = array_pad(explode('|', $scope), 2, null);
        if (null === $scopeCode) {
            $collection = $this->_scopeFactory->create(array('scope' => $scopeType));
        } else {
            $collection = $this->_scopeFactory->create(array('scope' => $scopeType, 'scopeId' => $scopeCode));
        }

        $config = array();
        foreach ($collection as $item) {
            $config[$item->getPath()] = $item->getValue();
        }
        return $this->_converter->convert($config);
    }
}
