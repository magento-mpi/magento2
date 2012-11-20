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
 * System configuration structure
 */
class Mage_Backend_Model_Config_Structure
{
    /**
     * Configuration structure represented as tree
     *
     * @var array
     */
    protected $_data;

    /**
     *
     * @var Mage_Backend_Model_Config_Structure_Element_Iterator
     */
    protected $_tabIterator;

    /**
     * @var Mage_Backend_Model_Config_Structure_Element_FlyweightPool
     */
    protected $_flyweightPool;

    /**
     * @param Mage_Backend_Model_Config_Structure_Reader $structureReader
     * @param Mage_Backend_Model_Config_Structure_Element_Iterator_Tab $tabIterator
     * @param Mage_Backend_Model_Config_Structure_Element_FlyweightPool $flyweightPool
     */
    public function __construct(
        Mage_Backend_Model_Config_Structure_Reader $structureReader,
        Mage_Backend_Model_Config_Structure_Element_Iterator_Tab $tabIterator,
        Mage_Backend_Model_Config_Structure_Element_FlyweightPool $flyweightPool
    ) {
        $this->_data = $structureReader->getData();
        $this->_tabIterator = $tabIterator;
        $this->_flyweightPool = $flyweightPool;
    }

    /**
     * Retrieve tab iterator
     *
     * @return Mage_Backend_Model_Config_Structure_Element_Iterator
     */
    public function getIterator()
    {
        $this->_tabIterator->setElements($this->_data['tabs']);
        return $this->_tabIterator;
    }

    /**
     * Find element by path
     *
     * @param string $path
     * @return Mage_Backend_Model_Config_Structure_ElementInterface
     */
    public function getElement($path)
    {
        $pathParts = explode('/', $path);
        $children = $this->_data['sections'];
        $child = array();
        foreach($pathParts as $id) {
            if (array_key_exists($id, $children)) {
                $child = $children[$id];
                $children = array_key_exists('children', $child) ? $child['children'] : array();
            } else {
                return null;
            }
        }
        return $this->_flyweightPool->getFlyweight($child);
    }

    /**
     * Check whether node has child node that can be shown
     *
     * @param Varien_Simplexml_Element $node
     * @param string $websiteCode
     * @param string $storeCode
     * @return boolean
     */
/*    public function hasChildren($node, $websiteCode = null, $storeCode = null)
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
    }*/

    /**
     * Checks whether it is possible to show the node
     *
     * @param mixed $node
     * @param string $websiteCode
     * @param string $storeCode
     * @return boolean
     */
/*    protected function _canShowNode($node, $websiteCode = null, $storeCode = null)
    {
        $showTab = false;
        if ($storeCode) {
            $showTab = isset($node['showInStore']) ? (int)$node['showInStore'] : false;
        } elseif ($websiteCode) {
            $showTab = isset($node['showInWebsite']) ? (int)$node['showInWebsite'] : false;
        } elseif (isset($node['showInDefault']) && $node['showInDefault']) {
            $showTab = true;
        }

        $showTab = $showTab || $this->_app->isSingleStoreMode();
        $showTab = $showTab && !($this->_app->isSingleStoreMode()
            && isset($node['hide_in_single_store_mode']) && $node['hide_in_single_store_mode']);
        return $showTab;
    }*/

    /**
     * Get translate module name
     *
     * @param array $sectionNode
     * @param array $groupNode
     * @param array $fieldNode
     * @return string
     */
/*    public function getAttributeModule($sectionNode = null, $groupNode = null, $fieldNode = null)
    {
        $moduleName = 'Mage_Backend';
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
    }*/

    /**
     * System configuration section, fieldset or field label getter
     *
     * @param string $sectionName
     * @param string $groupName
     * @param string $fieldName
     * @throws InvalidArgumentException
     * @return string
     */
/*    public function getSystemConfigNodeLabel($sectionName, $groupName = null, $fieldName = null)
    {
        $sectionName = trim($sectionName, '/');
        $groupNode = $fieldNode = null;
        $sectionNode = isset($this->_data['sections'][$sectionName]) ? $this->_data['sections'][$sectionName] : null;
        if (!$sectionNode) {
            throw new InvalidArgumentException(
                $this->_helperFactory->get('Mage_Backend_Helper_Data')->__('Wrong section specified.')
            );
        }
        $currentNode = $sectionNode;
        if (!empty($groupName)) {
            $groupName = trim($groupName, '/');
            $groupNode = isset($sectionNode['groups'][$groupName]) ? $sectionNode['groups'][$groupName] : null;
            if (!$groupNode) {
                throw new InvalidArgumentException(
                    $this->_helperFactory->get('Mage_Backend_Helper_Data')->__('Wrong group specified.')
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
                        $this->_helperFactory->get('Mage_Backend_Helper_Data')->__('Wrong field specified.')
                    );
                }
                $currentNode = $fieldNode;
            } else {
                Mage::throwException(
                    $this->_helperFactory->get('Mage_Backend_Helper_Data')->__('The group node name must be specified with field node name.')
                );
            }
        }
        $moduleName = $this->getAttributeModule($sectionNode, $groupNode, $fieldNode);
        return isset($currentNode['label'])
            ? $this->_helperFactory->get($moduleName)->__((string)$currentNode['label'])
            : '';
    }*/

    /**
     * Look for encrypted node entries in all system.xml files and return them
     *
     * @param bool $explodePathToEntities
     * @return array
     */
/*    public function getEncryptedNodeEntriesPaths($explodePathToEntities = false)
    {
        if (!$this->_encryptedPaths) {
            $this->_encryptedPaths = $this->getFieldsByAttribute(
                'backend_model', 'Mage_Backend_Model_Config_Backend_Encrypted', $explodePathToEntities
            );
        }
        return $this->_encryptedPaths;
    }*/

/*    public function getFieldsByAttribute($attributeName, $attributeValue, $explodePathToEntities = false)
    {
        $result = array();
        foreach ($this->_data['sections'] as $section) {
            if (!isset($section['groups'])) {
                continue;
            }
            foreach ($section['groups'] as $group) {
                if (!isset($group['fields'])) {
                    continue;
                }
                foreach ($group['fields'] as $field) {
                    if (isset($field[$attributeName])
                        && $field[$attributeName] == $attributeValue
                    ) {
                        if ($explodePathToEntities) {
                            $result[] = array(
                                'section' => $section['id'], 'group' => $group['id'], 'field' => $field['id']
                            );
                        } else {
                            $result[] = $section['id'] . '/' . $group['id'] . '/' . $field['id'];
                        }
                    }
                }
            }
        }
        return $result;
    }*/
}
