<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Search engine provider
 */
namespace Magento\CatalogSearch\Model\Resource;

use Magento\Store\Model\ScopeInterface;

class EngineProvider
{
    const CONFIG_ENGINE_PATH = 'catalog/search/engine';

    /**
     * @var \Magento\CatalogSearch\Model\Resource\EngineInterface
     */
    protected $_engine;

    /**
     * @var \Magento\CatalogSearch\Model\Resource\EngineFactory
     */
    protected $_engineFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @param \Magento\CatalogSearch\Model\Resource\EngineFactory $engineFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\CatalogSearch\Model\Resource\EngineFactory $engineFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_engineFactory = $engineFactory;
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * Get engine singleton
     *
     * @return \Magento\CatalogSearch\Model\Resource\EngineInterface
     */
    public function get()
    {
        if (!$this->_engine) {
            $engineClassName = $this->_scopeConfig->getValue(self::CONFIG_ENGINE_PATH, ScopeInterface::SCOPE_STORE);

            /**
             * This needed if there already was saved in configuration some none-default engine
             * and module of that engine was disabled after that.
             * Problem is in this engine in database configuration still set.
             */
            if ($engineClassName) {
                $engine = $this->_engineFactory->create($engineClassName);
                if ($engine && $engine->test()) {
                    $this->_engine = $engine;
                }
            }
        }

        return $this->_engine;
    }
}
