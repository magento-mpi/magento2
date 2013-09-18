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
 *
 * @category   Magento
 * @package    Magento_Persistent
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Persistent_Model_Persistent_Config
{
    /**
     * Path to config file
     *
     * @var string
     */
    protected $_configFilePath;

    /** @var Magento_Config_DomFactory  */
    protected $_domFactory;

    /** @var Magento_Persistent_Model_Persistent_Config_Converter  */
    protected $_converter;

    /** @var Magento_Core_Model_Config_Modules_Reader  */
    protected $_moduleReader;

    /** @var DOMDocument  */
    protected $_configDomDocument = null;

    /**
     * Constructor
     *
     * @param Magento_Config_DomFactory $domFactory
     * @param Magento_Persistent_Model_Persistent_Config_Converter $converter
     * @param Magento_Core_Model_Config_Modules_Reader $moduleReader
     */
    public function __construct(
        Magento_Config_DomFactory $domFactory,
        Magento_Persistent_Model_Persistent_Config_Converter $converter,
        Magento_Core_Model_Config_Modules_Reader $moduleReader
    ) {
        $this->_domFactory = $domFactory;
        $this->_converter = $converter;
        $this->_moduleReader = $moduleReader;
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
        return $this;
    }

    /**
     * Load persistent XML config
     *
     * @return DOMDocument
     * @throws Magento_Core_Exception
     */
    protected function _getConfigDomDocument()
    {
        if (is_null($this->_configDomDocument)) {
            $filePath = $this->_configFilePath;
            if (!is_file($filePath) || !is_readable($filePath)) {
                Mage::throwException(__('We cannot load the configuration from file %1.', $filePath));
            }
            $xml = file_get_contents($filePath);
            /** @var Magento_Config_DomFactory $configDom */
            $configDom = $this->_domFactory->createDom(
                array(
                    'xml' => $xml,
                    'idAttributes' => array(
                        'config/instances/blocks/reference' => 'id',
                    ),
                    'schemaFile' => $this->_moduleReader
                        ->getModuleDir('etc', 'Magento_Persistent') . '/persistent.xsd'
                )
            );
            $this->_configDomDocument = $configDom->getDom();
        }
        return $this->_configDomDocument;

    }

    /**
     * Get blocks by xpath
     * @param string $xpath
     * @return $array
     */
    public function getBlocks($xpath)
    {
        $domXPath = new DOMXPath($this->_getConfigDomDocument());
        $blocks = $domXPath->query($xpath);
        return $this->_converter->convertBlocks($blocks);
    }

    /**
     * Retrieve instances that should be emulated by persistent data
     *
     * @return array
     */
    public function collectInstancesToEmulate()
    {
        return $this->_converter->convert($this->_getConfigDomDocument());
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
     * @return Magento_Persistent_Model_Persistent_Config
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
