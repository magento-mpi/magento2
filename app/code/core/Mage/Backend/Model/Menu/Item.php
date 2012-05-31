<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Model_Menu_Item
{
    /**
     * Submenu item list
     *
     * @var Mage_Backend_Model_Menu
     */
    protected $_submenu;

    /**
     * Menu item id
     *
     * @var string
     */
    protected $_id;

    /**
     * Parent menu item id
     *
     * @var string
     */
    protected $_parentId = null;

    /**
     * Menu item action
     *
     * @var string
     */
    protected $_action = null;

    /**
     * Path from root element in tree
     *
     * @var string
     */
    protected $_path = '';

    /**
     * Menu item sort index in list
     *
     * @var string
     */
    protected $_sortIndex = null;

    /**
     * @var Mage_Backend_Model_Auth_Session
     */
    protected $_acl;

    /**
     * @param array $data
     */
    protected function __construct(array $data = array())
    {
        if (!isset($data['acl']) || !$data['acl'] instanceof Mage_Backend_Model_Auth_Session) {
            throw new InvalidArgumentException();
        }
        $this->_acl = $data['acl'];
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @return bool
     */
    public function hasParent()
    {
        return (bool) $this->_parentId;
    }

    /**
     * @return null|string
     */
    public function getParent()
    {
        return $this->_parentId;
    }

    /**
     * @return bool
     */
    public function hasSortIndex()
    {
        return (bool) $this->_sortIndex;
    }

    /**
     * @return int
     */
    public function getSortIndex()
    {
        return $this->_sortIndex;
    }

    /**
     * @return bool
     */
    public function hasChildren()
    {
        return (bool) $this->_submenu->count();
    }

    /**
     * @return Mage_Backend_Model_Menu
     */
    public function getChildren()
    {
        return $this->_submenu;
    }

    public function isActive()
    {
        return ($this->getActive()==$path.$childName)
            || (strpos($this->getActive(), $path.$childName.'/')===0);
    }

    /**
     * Retrieve full path from root element
     *
     * @return string
     */
    public function getFullPath()
    {
        return $this->_path . $this->_id;
    }

    public function getUrl()
    {
        if ((bool) $this->_action) {
            return $this->_url->getUrl((string)$this->_action, array('_cache_secret_key' => true));
        }
        return '#';
    }

    public function hasTitle()
    {

    }

    public function getTitle()
    {

    }

    public function getLevel()
    {

    }
    /**
     * Chechk whether item has javascript callback on click
     *
     * @return bool
     */
    public function hasClickCallback()
    {
        return $this->getUrl() == '#';
    }

    /**
     * Retrieve item click callback
     *
     * @return bool
     */
    public function getClickCallback()
    {
        if ($this->getUrl() == '#') {
            return 'return false;';
        }
        return '';
    }

    public function getLabel()
    {
        $helperName         = isset($this->_data['module']) ? $this->_data['module'] : 'Mage_Backend_Helper_Data';
        $titleNodeName      = 'title';
        $childAttributes    = $child->attributes();
        if (isset($childAttributes['module'])) {
            $helperName     = (string)$childAttributes['module'];
        }
//        if (isset($childAttributes['translate'])) {
//            $titleNodeName  = (string)$childAttributes['translate'];
//        }

        return Mage::helper($helperName)->__((string)$child->$titleNodeName);
    }

    public function isDisabled()
    {
        if (!$this->_isEnabledModuleOutput($child) || $child->depends && !$this->_checkDepends($child->depends)) {
            return false;
        }
    }

    /**
     * Check Depends
     *
     * @param Varien_Simplexml_Element $depends
     * @return bool
     */
    protected function _checkDepends(Varien_Simplexml_Element $depends)
    {
        if ($this->_dependsOnModule) {
            $module = $this->_dependsOnModule;
            $modulesConfig = Mage::getConfig()->getNode('modules');
            if (!$modulesConfig->$module || !$modulesConfig->$module->is('active')) {
                    return false;
            }
        }

        if ($this->_dependsOnConfig) {
            if (!Mage::getStoreConfigFlag((string)$this->_dependsOnConfig)) {
                return false;
            }
        }
        return true;
    }

    public function isAllowed()
    {
        $aclResource = 'admin/' . ($this->_resource ? (string)$this->_resource : $this->getFullPath());
        try {
            $res =  $this->_acl->isAllowed($resource);
        } catch (Exception $e) {
            return false;
        }
        return $res;
        if (!$this->_checkAcl($aclResource)){
            return true;
        }
    }

    public function addChild(Mage_Backend_Model_Menu_Item $item)
    {
        if (!$this->_submenu) {
            $this->_submenu = new Mage_Backend_Model_Menu();
        }
        $this->_submenu->addChild($item);
    }

    public function setParent(Mage_Backend_Model_Menu $menu)
    {
        $this->_path = $menu->getFullPath();
    }
    /**
     * @param array $data
     */
    public function addData(array $data)
    {
        $this->_id = $data['id'];
        $this->_parentId = isset($data['parent']) ? $data['parent'] : null;
    }

    /**
     * @param array $data
     */
    public function updateData(array $data)
    {
    }
}
