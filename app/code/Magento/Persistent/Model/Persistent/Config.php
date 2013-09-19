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

    /** @var Magento_Core_Model_Config_Modules_Reader  */
    protected $_moduleReader;

    /** @var DOMXPath  */
    protected $_configDomXPath = null;

    /**
     * Constructor
     *
     * @param Magento_Config_DomFactory $domFactory
     * @param Magento_Core_Model_Config_Modules_Reader $moduleReader
     */
    public function __construct(
        Magento_Config_DomFactory $domFactory,
        Magento_Core_Model_Config_Modules_Reader $moduleReader
    ) {
        $this->_domFactory = $domFactory;
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
     * Get persistent XML config xpath
     *
     * @return DOMXPath
     * @throws Magento_Core_Exception
     */
    protected function _getConfigDomXPath()
    {
        if (is_null($this->_configDomXPath)) {
            $filePath = $this->_configFilePath;
            if (!is_file($filePath) || !is_readable($filePath)) {
                Mage::throwException(__('We cannot load the configuration from file %1.', $filePath));
            }
            $xml = file_get_contents($filePath);
            /** @var Magento_Config_Dom $configDom */
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
            $this->_configDomXPath = new DOMXPath($configDom->getDom());
        }
        return $this->_configDomXPath;

    }

    /**
     * Get block's persistent config info.
     *
     * @param string $block
     * @return $array
     */
    public function getBlockConfigInfo($block)
    {
        $xPath = '//instances/blocks/*[block_type="' . $block . '"]';
        $blocks = $this->_getConfigDomXPath()->query($xPath);
        return $this->_convertBlocksToArray($blocks);
    }

    /**
     * Retrieve instances that should be emulated by persistent data
     *
     * @return array
     */
    public function collectInstancesToEmulate()
    {
        $xPath = '/config/instances/blocks/reference';
        $blocks = $this->_getConfigDomXPath()->query($xPath);
        $blocksArray = $this->_convertBlocksToArray($blocks);
        return array('blocks' => $blocksArray);
    }

    /**
     * Convert Blocks
     *
     * @param DomNodeList $blocks
     * @return array
     */
    protected function _convertBlocksToArray($blocks)
    {
        $blocksArray = array();
        foreach ($blocks as $reference) {
            $referenceAttributes = $reference->attributes;
            $id = $referenceAttributes->getNamedItem('id')->nodeValue;
            $blocksArray[$id] = array();
            /** @var $referenceSubNode DOMNode */
            foreach ($reference->childNodes as $referenceSubNode) {
                switch ($referenceSubNode->nodeName) {
                    case 'name_in_layout':
                    case 'class':
                    case 'method':
                    case 'block_type':
                        $blocksArray[$id][$referenceSubNode->nodeName] = $referenceSubNode->nodeValue;
                        break;
                    default:
                }
            }
        }
        return $blocksArray;
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
