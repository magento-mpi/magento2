<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Magento_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Search engine provider
 */
class Magento_CatalogSearch_Model_Resource_EngineProvider
{
    /**
     * @var Magento_CatalogSearch_Model_Resource_EngineInterface
     */
    protected $_engine;

    /**
     * @var Magento_CatalogSearch_Model_Resource_EngineFactory
     */
    protected $_engineFactory;

    /**
     * @var Magento_Core_Model_Store_ConfigInterface
     */
    protected $_storeConfig;

    /**
     * @param Magento_CatalogSearch_Model_Resource_EngineFactory $engineFactory
     * @param Magento_Core_Model_Store_ConfigInterface $storeConfig
     */
    public function __construct(
        Magento_CatalogSearch_Model_Resource_EngineFactory $engineFactory,
        Magento_Core_Model_Store_ConfigInterface $storeConfig
    ) {
        $this->_engineFactory = $engineFactory;
        $this->_storeConfig = $storeConfig;
    }

    /**
     * Get engine singleton
     *
     * @return Magento_CatalogSearch_Model_Resource_EngineInterface
     */
    public function get()
    {
        if (!$this->_engine) {
            $engineClassName = $this->_storeConfig->getConfig('catalog/search/engine');

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
            if (!$this->_engine) {
                $this->_engine = $this->_engineFactory->create('Magento_CatalogSearch_Model_Resource_Fulltext_Engine');
            }
        }

        return $this->_engine;
    }
}
