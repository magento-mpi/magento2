<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System configuration structure reader
 */
class Mage_Backend_Model_Config_Structure extends Magento_Config_XmlAbstract
    implements Mage_Backend_Model_Config_StructureInterface
{
    /**
     * Main Application object
     *
     * @var Mage_Core_Model_App
     */
    protected $_app;

    /**
     * Config structure toArray converter
     *
     * @var Mage_Backend_Model_Config_Structure_Converter
     */
    protected $_converter;

    /**
     * List of encrypted paths
     *
     * @var array
     */
    protected $_encryptedPaths = array();

    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->_app = isset($data['app']) ? $data['app'] : Mage::app();
        $this->_converter = isset($data['converter'])
            ? $data['converter']
            : Mage::getSingleton('Mage_Backend_Model_Config_Structure_Converter');
        parent::__construct($data['sourceFiles']);
    }

    public function __wakeUp()
    {
        $this->_app = Mage::app();
    }

    /**
     * Get absolute path to the XML-schema file
     *
     * @return string
     */
    public function getSchemaFile()
    {
        return __DIR__ . '/Structure/system.xsd';
    }

    /**
     * Extract configuration data from the DOM structure
     *
     * @param DOMDocument $dom
     * @return array|DOMNodeList
     */
    protected function _extractData(DOMDocument $dom)
    {
        $data = $this->_converter->convert($dom);
        return $data['config']['system'];
    }

    /**
     * Get XML-contents, initial for merging
     *
     * @return string
     */
    protected function _getInitialXml()
    {
        return '<?xml version="1.0" encoding="utf-8"?><config><system></system></config>';
    }

    /**
     * Get list of paths to identifiable nodes
     *
     * @return array
     */
    protected function _getIdAttributes()
    {
        return array(
            '/config/system/tab' => 'id',
            '/config/system/section' => 'id',
            '/config/system/section/group' => 'id',
            '/config/system/section/group/field' => 'id',
        );
    }

    /**
     * Retrieve all sections system configuration layout
     *
     * @return array
     */
    public function getSections()
    {
        return $this->_data['sections'];
    }

    /**
     * Retrieve list of tabs from
     *
     * @return array
     */
    public function getTabs()
    {
        return $this->_data['tabs'];
    }

    /**
     * Retrieve defined section
     *
     * @param string $sectionCode
     * @param string $websiteCode
     * @param string $storeCode
     * @return array
     */
    public function getSection($sectionCode=null, $websiteCode=null, $storeCode=null)
    {
        $key = $sectionCode ?: $websiteCode ?: $storeCode;
        return isset($this->_data['sections'][$key]) ? $this->_data['sections'][$key] : null;
    }

    /**
     * Check whether node has child node that can be shown
     *
     * @param Varien_Simplexml_Element $node
     * @param string $websiteCode
     * @param string $storeCode
     * @return boolean
     */
    public function hasChildren($node, $websiteCode = null, $storeCode = null)
    {
        if (!$this->_canShowNode($node, $websiteCode, $storeCode)) {
            return false;
        }

        if (isset($node['groups'])) {
            $children = $node['groups'];
        } elseif (isset($node['fields'])) {
            $children = $node['fields'];
        } else {
            return true;
        }

        foreach ($children as $child) {
            if ($this->hasChildren($child, $websiteCode, $storeCode)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Checks whether it is possible to show the node
     *
     * @param mixed $node
     * @param string $websiteCode
     * @param string $storeCode
     * @return boolean
     */
    protected function _canShowNode($node, $websiteCode = null, $storeCode = null)
    {
        $showTab = false;
        if ($storeCode) {
            $showTab = isset($node['showInStore']) ? (int)$node['showInStore'] : 0;
        } elseif ($websiteCode) {
            $showTab = isset($node['showInWebsite']) ? (int)$node['showInWebsite'] : 0;
        } elseif (isset($node['showInDefault']) && isset($node['showInWebsite']) && (int)$node['showInWebsite']) {
            $showTab = true;
        }

        $showTab = $showTab || $this->_app->isSingleStoreMode();
        $showTab = $showTab && !($this->_app->isSingleStoreMode()
            && isset($node['hide_in_single_store_mode']) && $node['hide_in_single_store_mode']);
        return $showTab;
    }

    /**
     * Get translate module name
     *
     * @param array $sectionNode
     * @param array $groupNode
     * @param array $fieldNode
     * @return string
     */
    public function getAttributeModule($sectionNode = null, $groupNode = null, $fieldNode = null)
    {
        $moduleName = 'Mage_Adminhtml';
        if (isset($sectionNode['module'])) {
            $moduleName = (string) $sectionNode['module'];
        }
        if (isset($groupNode['module'])) {
            $moduleName = (string) $groupNode['module'];
        }
        if (isset($fieldNode['module'])) {
            $moduleName = (string) $fieldNode['module'];
        }
        return $moduleName;
    }

    /**
     * System configuration section, fieldset or field label getter
     *
     * @param string $sectionName
     * @param string $groupName
     * @param string $fieldName
     * @throws InvalidArgumentException
     * @return string
     */
    public function getSystemConfigNodeLabel($sectionName, $groupName = null, $fieldName = null)
    {
        $sectionName = trim($sectionName, '/');
        $groupNode = $fieldNode = null;
        $sectionNode = isset($this->_data['sections'][$sectionName]) ? $this->_data['sections'][$sectionName] : null;
        if (!$sectionNode) {
            throw new InvalidArgumentException(
                Mage::helper('Mage_Adminhtml_Helper_Data')->__('Wrong section specified.')
            );
        }
        $currentNode = $sectionNode;
        if (!empty($groupName)) {
            $groupName = trim($groupName, '/');
            $groupNode = isset($sectionNode['groups'][$groupName]) ? $sectionNode['groups'][$groupName] : null;
            if (!$groupNode) {
                throw new InvalidArgumentException(
                    Mage::helper('Mage_Adminhtml_Helper_Data')->__('Wrong group specified.')
                );
            }
            $currentNode = $groupNode;
        }
        if (!empty($fieldName)) {
            if (!empty($groupNode)) {
                $fieldName = trim($fieldName, '/');
                $fieldNode = isset($groupNode['fields'][$fieldName]) ? $groupNode['fields'][$fieldName] : null;
                if (!$fieldNode) {
                    throw new InvalidArgumentException(
                        Mage::helper('Mage_Adminhtml_Helper_Data')->__('Wrong field specified.')
                    );
                }
                $currentNode = $fieldNode;
            } else {
                Mage::throwException(Mage::helper('Mage_Adminhtml_Helper_Data')->__('The group node name must be specified with field node name.'));
            }
        }
        $moduleName = $this->getAttributeModule($sectionNode, $groupNode, $fieldNode);
        return isset($currentNode['label']) ? Mage::helper($moduleName)->__((string)$currentNode['label']) : '';
    }

    /**
     * Look for encrypted node entries in all system.xml files and return them
     *
     * @param bool $explodePathToEntities
     * @return array
     */
    public function getEncryptedNodeEntriesPaths($explodePathToEntities = false)
    {
        foreach ($this->_data['sections'] as $section) {
            if (!isset($section['groups'])) {
                continue;
            }
            foreach ($section['groups'] as $group) {
                if (!isset($group['fields'])) {
                    continue;
                }
                foreach ($group['fields'] as $field) {
                    if (isset($field['backend_model'])
                        && $field['backend_model'] == 'Mage_Backend_Model_Config_Backend_Encrypted'
                    ) {
                        if ($explodePathToEntities) {
                            $this->_encryptedPaths[] = array(
                                'section' => $section['id'], 'group' => $group['id'], 'field' => $field['id']
                            );
                        } else {
                            $this->_encryptedPaths[] = $section['id'] . '/' . $group['id'] . '/' . $field['id'];
                        }
                    }
                }
            }
        }
        return $this->_encryptedPaths;
    }
}
