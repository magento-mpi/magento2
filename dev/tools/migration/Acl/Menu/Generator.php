<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Tools_Migration_Acl_Menu_Generator
{
    /**
     * @var array
     */
    protected $_menuFiles;

    /**
     * @var string
     */
    protected $_basePath;

    /**
     * @var string
     */
    protected $_validNodeTypes;

    /**
     * @var array
     */
    protected $_menuIdMaps = array();

    /**
     * @var array
     */
    protected $_idToXPath = array();

    /**
     * @var array
     */
    protected $_aclXPathToId = array();

    /**
     * @var array
     */
    protected $_menuIdToAclId = array();

    /**
     * @var array
     */
    protected $_menuDomList = array();

    /**
     * @var array
     */
    protected $_updateNodes = array();

    /**
     * Is preview mode
     *
     * @var bool
     */
    protected $_isPreviewMode;

    /**
     * @var Tools_Migration_Acl_FileWriter
     */
    protected $_fileWriter;


    /**
     * @param $basePath
     * @param $validNodeTypes
     * @param $aclXPathToId
     * @param Tools_Migration_Acl_FileWriter $fileWriter
     * @param bool $preview
     */
    public function __construct(
        $basePath,
        $validNodeTypes,
        $aclXPathToId,
        Tools_Migration_Acl_FileWriter $fileWriter,
        $preview = true
    ) {
        $this->_fileWriter = $fileWriter;
        $this->_basePath = $basePath;
        $this->_validNodeTypes = $validNodeTypes;
        $this->_aclXPathToId = $aclXPathToId;
        $this->_updateNodes = array(
            'add' => array(
                'required' => true,
                'attribute' => 'resource',
            ),
            'update' => array(
                'required' => false,
                'attribute' => 'resource',
            ),
        );

        $this->_isPreviewMode = $preview;
    }

    /**
     * Get etc directory pattern
     *
     * @return null|string
     */
    public function getEtcDirPattern()
    {
        return $this->_basePath . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'code' . DIRECTORY_SEPARATOR
            . '*' . DIRECTORY_SEPARATOR //code pool
            . '*' . DIRECTORY_SEPARATOR //namespace
            . '*' . DIRECTORY_SEPARATOR //module name
            . 'etc' . DIRECTORY_SEPARATOR;
    }

    /**
     * @return array|null
     */
    public function getMenuFiles()
    {
        if (null === $this->_menuFiles) {
            $pattern = $this->getEtcDirPattern() . 'adminhtml' . DIRECTORY_SEPARATOR . 'menu.xml';
            $this->_menuFiles = (glob($pattern));
        }
        return $this->_menuFiles;
    }

    /**
     * Parse menu item node
     *
     * @param DOMNode $node
     */
    public function parseMenuNode(DOMNode $node)
    {
        /** @var $childNode DOMNode **/
        foreach ($node->childNodes as $childNode) {
            if (false == in_array($childNode->nodeType, $this->_validNodeTypes) || 'add' != $childNode->nodeName) {
                continue;
            }
            $this->_menuIdMaps[$childNode->getAttribute('id')]['parent'] = $childNode->getAttribute('parent');
            $this->_menuIdMaps[$childNode->getAttribute('id')]['resource'] = $childNode->getAttribute('resource');
        }
    }

    /**
     * @return array
     */
    public function getMenuIdMaps()
    {
        return $this->_menuIdMaps;
    }

    /**
     * Parse menu files
     */
    public function parseMenuFiles()
    {
        foreach ($this->getMenuFiles() as $file) {
            $dom = new DOMDocument();
            $dom->load($file);
            $this->_menuDomList[$file] = $dom;
            $menus = $dom->getElementsByTagName('menu');

            /** @var $menuNode DOMNode **/
            foreach ($menus as $menuNode) {
                $this->parseMenuNode($menuNode);
            }
        }
    }

    /**
     * @return array
     */
    public function getMenuDomList()
    {
        return $this->_menuDomList;
    }

    /**
     * @param $menuId
     */
    public function initParentItems($menuId)
    {
        $this->_menuIdMaps[$menuId]['parents'] = array();
        $parentId = $this->_menuIdMaps[$menuId]['parent'];
        while ($parentId) {
            $this->_menuIdMaps[$menuId]['parents'][] = $parentId;
            if (false == isset($this->_menuIdMaps[$parentId])) {
                return;
            }
            $parentId = $this->_menuIdMaps[$parentId]['parent'];
        }
    }

    /**
     * Build xpath elements
     *
     * @param $menuId
     */
    public function buildXPath($menuId)
    {
        $parents = $this->_menuIdMaps[$menuId]['parents'] ?
            $this->_menuIdMaps[$menuId]['parents'] :
            array();
        $resource = $this->_menuIdMaps[$menuId]['resource'];
        if (!$resource) {
            $parts = array();
            $parents = array_reverse($parents);
            $parents[] = $menuId;

            foreach ($parents as $parent) {
                $parentParts = explode('::', $parent);
                $idPart = $parentParts[1];
                $prevParts = implode('_', $parts);
                $start = strpos($prevParts, $idPart) + strlen($prevParts);
                $id = substr($idPart, $start);
                $parts[] = trim($id, '_');
            }
            $resource = implode('/', $parts);
        }

        $this->_idToXPath[$menuId] = $resource;
    }

    /**
     * @return array
     */
    public function getIdToXPath()
    {
        return $this->_idToXPath;
    }

    /**
     * Initialize menu items XPath
     */
    public function buildMenuItemsXPath()
    {
        foreach (array_keys($this->_menuIdMaps) as $menuId) {
            $this->initParentItems($menuId);
            $this->buildXPath($menuId);
        }
    }

    /**
     * Map menu item id to ACL resource id
     *
     * @return array
     */
    public function mapMenuToAcl()
    {
        $output = array(
            'mapped' => array(),
            'not_mapped' => array(),
        );
        $aclPrefix = 'config/acl/resources/admin/';
        foreach ($this->_idToXPath as $menuId => $menuXPath) {
            $key = $aclPrefix . $menuXPath;
            if (isset($this->_aclXPathToId[$key])) {
                $this->_menuIdToAclId[$menuId] = $this->_aclXPathToId[$key];
                $output['mapped'][] = $menuId;
            } else {
                $output['not_mapped'][] = $menuId;
            }
        }

        $output['artifacts']['MenuIdToAclId.log'] = json_encode($this->_menuIdToAclId);
        return $output;
    }

    /**
     * @return array
     */
    public function getMenuIdToAclId()
    {
        return $this->_menuIdToAclId;
    }

    /**
     * @param array $idToXPath
     */
    public function setIdToXPath($idToXPath)
    {
        $this->_idToXPath = $idToXPath;
    }

    /**
     * Update attributes of menu items to set ACL resource id
     *
     * @return array
     */
    public function updateMenuAttributes()
    {
        $errors = array();
        $aclPrefix = 'config/acl/resources/admin/';
        /** @var $dom DOMDocument **/
        foreach ($this->_menuDomList as $file => $dom) {
            $menu = $dom->getElementsByTagName('menu')->item(0);
                /** @var $childNode DOMNode **/
            foreach ($menu->childNodes as $childNode) {

                if (!$this->_isNodeValidToUpdate($childNode)) {
                    continue;
                }

                $attributeName = $this->_updateNodes[$childNode->nodeName]['attribute'];
                $required = $this->_updateNodes[$childNode->nodeName]['required'];
                $resource = $childNode->getAttribute($attributeName);
                $menuId = $childNode->getAttribute('id');

                if (false == array_key_exists($menuId, $this->_menuIdToAclId)) {
                    $errors[] = 'File: ' . $file . ' :: Menu: ' . $menuId . ' is not mapped to ACL id';
                    continue;
                }
                $aclId = $this->_menuIdToAclId[$menuId];

                if ($resource) {
                    $aclXPath = $aclPrefix . $resource;
                    if (false == array_key_exists($aclXPath, $this->_aclXPathToId)) {
                        $errors[] = 'File: ' . $file . ' :: Menu: ' . $menuId
                            . '. There is no ACL resource with XPath ' . $aclXPath;
                        continue;
                    }
                    $aclId = $this->_aclXPathToId[$aclXPath];
                }
                if ($required || $resource) {
                    $childNode->setAttribute($attributeName, $aclId);
                }
            }
        }

        return $errors;
    }

    /**
     * Check if node has to be updated
     *
     * @param DOMNode $node
     * @return bool
     */
    protected function _isNodeValidToUpdate(DOMNode $node)
    {
        if (false == in_array($node->nodeType, $this->_validNodeTypes) ||
            false == array_key_exists($node->nodeName, $this->_updateNodes)
        ) {
            return false;
        }

        return true;
    }

    /**
     * @param array $menuIdToAclId
     */
    public function setMenuIdToAclId($menuIdToAclId)
    {
        $this->_menuIdToAclId = $menuIdToAclId;
    }

    /**
     * @param array $aclXPathToId
     */
    public function setAclXPathToId($aclXPathToId)
    {
        $this->_aclXPathToId = $aclXPathToId;
    }

    /**
     * @param array $menuDomList
     */
    public function setMenuDomList($menuDomList)
    {
        $this->_menuDomList = $menuDomList;
    }

    /**
     * Save menu XML files
     */
    public function saveMenuFiles()
    {
        if (true == $this->_isPreviewMode) {
            return;
        }
        /** @var $dom DOMDocument **/
        foreach ($this->_menuDomList as $file => $dom) {
            $dom->formatOutput = true;
            $this->_fileWriter->write($file, $dom->saveXML());
        }
    }

    /**
     * @return array
     */
    public function run()
    {
        $this->parseMenuFiles();

        $this->buildMenuItemsXPath();

        $result = $this->mapMenuToAcl();

        $result['menu_update_errors'] = $this->updateMenuAttributes();

        $this->saveMenuFiles();

        return $result;
    }
}
