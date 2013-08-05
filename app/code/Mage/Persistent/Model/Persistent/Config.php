<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Persistent
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Persistent Config Model
 *
 * @category   Mage
 * @package    Mage_Persistent
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Persistent_Model_Persistent_Config
{
    /**
     * XML config instance for Persistent mode
     * @var null|Varien_Simplexml_Element
     */
    protected $_xmlConfig = null;

    /**
     * Path to config file
     *
     * @var string
     */
    protected $_configFilePath;

    /**
     * Set path to config file that should be loaded
     *
     * @param string $path
     * @return Mage_Persistent_Model_Persistent_Config
     */
    public function setConfigFilePath($path)
    {
        $this->_configFilePath = $path;
        $this->_xmlConfig = null;
        return $this;
    }

    /**
     * Load persistent XML config
     *
     * @return Varien_Simplexml_Element
     * @throws Mage_Core_Exception
     */
    public function getXmlConfig()
    {
        if (is_null($this->_xmlConfig)) {
            $filePath = $this->_configFilePath;
            if (!is_file($filePath) || !is_readable($filePath)) {
                Mage::throwException(__('We cannot load the configuration from file %s.', $filePath));
            }
            $xml = file_get_contents($filePath);
            $this->_xmlConfig = new Varien_Simplexml_Element($xml);
        }
        return $this->_xmlConfig;
    }

    /**
     * Retrieve instances that should be emulated by persistent data
     *
     * @return array
     */
    public function collectInstancesToEmulate()
    {
        $config = $this->getXmlConfig()->asArray();
        return $config['instances'];
    }

    /**
     * Run all methods declared in persistent configuration
     *
     * @return Mage_Persistent_Model_Persistent_Config
     */
    public function fire()
    {
        foreach ($this->collectInstancesToEmulate() as $type => $elements) {
            if (!is_array($elements)) {
                continue;
            }
            foreach ($elements as $info) {
                switch ($type) {
                    case 'blocks':
                        $this->fireOne($info, Mage::app()->getLayout()->getBlock($info['name_in_layout']));
                        break;
                }
            }
        }
        return $this;
    }

    /**
     * Run one method by given method info
     *
     * @param array $info
     * @param bool $instance
     * @return Mage_Persistent_Model_Persistent_Config
     */
    public function fireOne($info, $instance = false)
    {
        if (!$instance
            || (isset($info['block_type']) && !($instance instanceof $info['block_type']))
            || !isset($info['class'])
            || !isset($info['method'])
        ) {
            return $this;
        }
        $object     = Mage::getModel($info['class']);
        $method     = $info['method'];

        if (method_exists($object, $method)) {
            $object->$method($instance);
        } elseif (Mage::getIsDeveloperMode()) {
            Mage::throwException('Method "' . $method.'" is not defined in "' . get_class($object) . '"');
        }

        return $this;
    }
}
