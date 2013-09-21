<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Persistent
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Persistent Config Model
 */
class Magento_Persistent_Model_Persistent_Config
{
    /**
     * XML config instance for Persistent mode
     * @var null|Magento_Simplexml_Element
     */
    protected $_xmlConfig = null;

    /**
     * Path to config file
     *
     * @var string
     */
    protected $_configFilePath;

    /**
     * Layout model
     *
     * @var Magento_Core_Model_Layout
     */
    protected $_layout;

    /**
     * App state model
     *
     * @var Magento_Core_Model_App_State
     */
    protected $_appState;

    /**
     * Model factory
     *
     * @var Magento_Persistent_Model_Factory
     */
    protected $_factory;

    /**
     * Construct
     *
     * @param Magento_Core_Model_Layout $layout
     * @param Magento_Core_Model_App_State $appState
     * @param Magento_Persistent_Model_Factory $factory
     */
    public function __construct(
        Magento_Core_Model_Layout $layout,
        Magento_Core_Model_App_State $appState,
        Magento_Persistent_Model_Factory $factory
    ) {
        $this->_layout = $layout;
        $this->_appState = $appState;
        $this->_factory = $factory;
    }

    /**
     * Set path to config file that should be loaded
     *
     * @param string $path
     * @return Magento_Persistent_Model_Persistent_Config
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
     * @return Magento_Simplexml_Element
     * @throws Magento_Core_Exception
     */
    public function getXmlConfig()
    {
        if (is_null($this->_xmlConfig)) {
            $filePath = $this->_configFilePath;
            if (!is_file($filePath) || !is_readable($filePath)) {
                throw new Magento_Core_Exception(__('We cannot load the configuration from file %1.', $filePath));
            }
            $xml = file_get_contents($filePath);
            $this->_xmlConfig = new Magento_Simplexml_Element($xml);
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
     * @return Magento_Persistent_Model_Persistent_Config
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
                        $this->fireOne($info, $this->_layout->getBlock($info['name_in_layout']));
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
     * @return Magento_Persistent_Model_Persistent_Config
     * @throws Magento_Core_Exception
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
        $object = $this->_factory->create($info['class']);
        $method = $info['method'];

        if (method_exists($object, $method)) {
            $object->$method($instance);
        } elseif ($this->_appState->getMode() == Magento_Core_Model_App_State::MODE_DEVELOPER) {
            throw new Magento_Core_Exception('Method "' . $method.'" is not defined in "' . get_class($object) . '"');
        }

        return $this;
    }
}
