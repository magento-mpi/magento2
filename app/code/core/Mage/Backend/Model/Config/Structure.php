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
class Mage_Backend_Model_System_Config_Structure extends Magento_Config_XmlAbstract
    implements Mage_Backend_Model_System_ConfigInterface
{
    /**
     * Main Application object
     *
     * @var Mage_Core_Model_App
     */
    protected $_app;

    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->_app = isset($data['app']) ? $data['app'] : Mage::app();
    }

    /**
     * Get absolute path to the XML-schema file
     *
     * @return string
     */
    public function getSchemaFile()
    {
        return __DIR__ . '/config/system.xsd';
    }

    /**
     * Extract configuration data from the DOM structure
     *
     * @param DOMDocument $dom
     * @return array|DOMNodeList
     */
    protected function _extractData(DOMDocument $dom)
    {
        $converter = new Mage_Backend_Model_System_Config_Converter();
        $data = $converter->convert($dom);
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
        return $this->_data['sections'][$key];
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
        } elseif (isset($node['showInDefault']) && (int)$node['showInWebsite']) {
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
     * @param Varien_Simplexml_Element $sectionNode
     * @param Varien_Simplexml_Element $groupNode
     * @param Varien_Simplexml_Element $fieldNode
     * @return string
     */
    function getAttributeModule($sectionNode = null, $groupNode = null, $fieldNode = null)
    {
        $moduleName = 'Mage_Adminhtml';
        if (is_object($sectionNode) && method_exists($sectionNode, 'attributes')) {
            $sectionAttributes = $sectionNode->attributes();
            $moduleName = isset($sectionAttributes['module']) ? (string)$sectionAttributes['module'] : $moduleName;
        }
        if (is_object($groupNode) && method_exists($groupNode, 'attributes')) {
            $groupAttributes = $groupNode->attributes();
            $moduleName = isset($groupAttributes['module']) ? (string)$groupAttributes['module'] : $moduleName;
        }
        if (is_object($fieldNode) && method_exists($fieldNode, 'attributes')) {
            $fieldAttributes = $fieldNode->attributes();
            $moduleName = isset($fieldAttributes['module']) ? (string)$fieldAttributes['module'] : $moduleName;
        }

        return $moduleName;
    }

    /**
     * System configuration section, fieldset or field label getter
     *
     * @param string $sectionName
     * @param string $groupName
     * @param string $fieldName
     * @return string
     */
    public function getSystemConfigNodeLabel($sectionName, $groupName = null, $fieldName = null)
    {
        $sectionName = trim($sectionName, '/');
        $path = '//sections/' . $sectionName;
        $groupNode = $fieldNode = null;
        $sectionNode = $this->_sections->xpath($path);
        if (!empty($groupName)) {
            $path .= '/groups/' . trim($groupName, '/');
            $groupNode = $this->_sections->xpath($path);
        }
        if (!empty($fieldName)) {
            if (!empty($groupName)) {
                $path .= '/fields/' . trim($fieldName, '/');
                $fieldNode = $this->_sections->xpath($path);
            }
            else {
                Mage::throwException(Mage::helper('Mage_Adminhtml_Helper_Data')->__('The group node name must be specified with field node name.'));
            }
        }
        $moduleName = $this->getAttributeModule($sectionNode, $groupNode, $fieldNode);
        $systemNode = $this->_sections->xpath($path);
        foreach ($systemNode as $node) {
            return Mage::helper($moduleName)->__((string)$node->label);
        }
        return '';
    }

    /**
     * Look for encrypted node entries in all system.xml files and return them
     *
     * @return array $paths
     */
    public function getEncryptedNodeEntriesPaths($explodePathToEntities = false)
    {
        $paths = array();
        $configSections = $this->getSections();
        if ($configSections) {
            foreach ($configSections->xpath('//sections/*/groups/*/fields/*/backend_model') as $node) {
                if ('adminhtml/system_config_backend_encrypted' === (string)$node) {
                    $section = $node->getParent()->getParent()->getParent()->getParent()->getParent()->getName();
                    $group   = $node->getParent()->getParent()->getParent()->getName();
                    $field   = $node->getParent()->getName();
                    if ($explodePathToEntities) {
                        $paths[] = array('section' => $section, 'group' => $group, 'field' => $field);
                    }
                    else {
                        $paths[] = $section . '/' . $group . '/' . $field;
                    }
                }
            }
        }
        return $paths;
    }

}
