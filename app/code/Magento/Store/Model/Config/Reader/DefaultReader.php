<?php
/**
 * Default configuration reader
 *
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Store\Model\Config\Reader;

class DefaultReader implements \Magento\Framework\App\Config\Scope\ReaderInterface
{
    /**
     * @var \Magento\Framework\App\Config\Initial
     */
    protected $_initialConfig;

    /**
     * @var \Magento\Framework\App\Config\Scope\Converter
     */
    protected $_converter;

    /**
     * @var \Magento\Store\Model\Resource\Config\Collection\ScopedFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * @param \Magento\Framework\App\Config\Initial $initialConfig
     * @param \Magento\Framework\App\Config\Scope\Converter $converter
     * @param \Magento\Store\Model\Resource\Config\Collection\ScopedFactory $collectionFactory
     * @param \Magento\Framework\App\State $appState
     */
    public function __construct(
        \Magento\Framework\App\Config\Initial $initialConfig,
        \Magento\Framework\App\Config\Scope\Converter $converter,
        \Magento\Store\Model\Resource\Config\Collection\ScopedFactory $collectionFactory,
        \Magento\Framework\App\State $appState
    ) {
        $this->_initialConfig = $initialConfig;
        $this->_converter = $converter;
        $this->_collectionFactory = $collectionFactory;
        $this->_appState = $appState;
    }

    /**
     * Read configuration data
     *
     * @return array
     */
    public function read()
    {
        $config = $this->_initialConfig->getData(\Magento\Framework\App\ScopeInterface::SCOPE_DEFAULT);

        $collection = $this->_collectionFactory->create(
            array('scope' => \Magento\Framework\App\ScopeInterface::SCOPE_DEFAULT)
        );
        $dbDefaultConfig = array();
        foreach ($collection as $item) {
            $dbDefaultConfig[$item->getPath()] = $item->getValue();
        }
        $dbDefaultConfig = $this->_converter->convert($dbDefaultConfig);
        $config = array_replace_recursive($config, $dbDefaultConfig);

        return $config;
    }
}
