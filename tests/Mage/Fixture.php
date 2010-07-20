<?php
/**
 * Helper for work with fixtures
 *
 * @author          Magento Core Team <core@magentocommerce.com>
 */
class Mage_Fixture
{
    /**
     * Original config
     *
     * @var SimplexmlElement
     */
    protected $_config;

    /**
     * Load fixture content from file
     *
     * @param string $path
     * @throws Exception
     * @return string
     */
    public function loadFixture($path)
    {
        $path = BP . DS . 'tests' . DS . $path;
        if (!file_exists($path)) {
            throw new Exception('Fixture file does not exists');
        }
        if (!is_readable($path)) {
            throw new Exception('Fixture file does not readable');
        }

        return file_get_contents($path);
    }

    /**
     * Retrieve a config instance wrapper
     *
     * @return Mage_Core_Model_Config
     */
    public function getConfig()
    {
        return Mage::getConfig();
    }

    /**
     * Apply changes to config
     *
     * @param string|Varien_Simplexml_Element $config
     * @param bool $toStores
     * @return Mage_Fixture
     */
    public function applyConfig($config = null, $toStores = false)
    {
        // backup original content
        if (is_null($this->_config)) {
            $this->_config = clone $this->getConfig()->getNode(null);
        }

        if (is_string($config)) {
            $config = @simplexml_load_string($config, 'Varien_Simplexml_Element');
        }
        if (!$config instanceof Varien_Simplexml_Element) {
            return $this;
        }

        if ($toStores) {
            $storeNodes = $this->getConfig()->getNode('stores')->children();
            foreach ($storeNodes as $storeNode) {
                $storeNode->extend($config, true);
            }
        } else {
            $this->getConfig()->getNode()->extend($config, true);
        }

        return $this;
    }

    /**
     * Restore original configuration
     *
     * @return Mage_Fixture
     */
    public function rollbackConfig()
    {
        if (!is_null($this->_config)) {
            $this->getConfig()->setXml($this->_config);
            $this->_config = null;
        }

        return $this;
    }
}
