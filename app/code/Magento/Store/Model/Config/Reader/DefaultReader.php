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

class DefaultReader implements \Magento\App\Config\Scope\ReaderInterface
{
    /**
     * @var \Magento\App\Config\Initial
     */
    protected $_initialConfig;

    /**
     * @var \Magento\App\Config\Scope\Converter
     */
    protected $_converter;

    /**
     * @var \Magento\Core\Model\Resource\Config\Value\Collection\ScopedFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\App\State
     */
    protected $_appState;

    /**
     * @param \Magento\App\Config\Initial $initialConfig
     * @param \Magento\App\Config\Scope\Converter $converter
     * @param \Magento\Core\Model\Resource\Config\Value\Collection\ScopedFactory $collectionFactory
     * @param \Magento\App\State $appState
     */
    public function __construct(
        \Magento\App\Config\Initial $initialConfig,
        \Magento\App\Config\Scope\Converter $converter,
        \Magento\Core\Model\Resource\Config\Value\Collection\ScopedFactory $collectionFactory,
        \Magento\App\State $appState
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
        $config = $this->_initialConfig->getData(\Magento\App\ScopeInterface::SCOPE_DEFAULT);
        if ($this->_appState->isInstalled()) {
            $collection = $this->_collectionFactory->create(
                array('scope' => \Magento\App\ScopeInterface::SCOPE_DEFAULT)
            );
            $dbDefaultConfig = array();
            foreach ($collection as $item) {
                $dbDefaultConfig[$item->getPath()] = $item->getValue();
            }
            $dbDefaultConfig = $this->_converter->convert($dbDefaultConfig);
            $config = array_replace_recursive($config, $dbDefaultConfig);
        }
        return $config;
    }
}
