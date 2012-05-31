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
     * Menu item id
     *
     * @var string
     */
    protected $_id;

    /**
     * Menu item title
     *
     * @var string
     */
    protected $_title;

    /**
     * Module of menu item
     *
     * @var string
     */
    protected $_moduleHelper;

    /**
     * Menu item sort index in list
     *
     * @var string
     */
    protected $_sortIndex = null;

    /**
     * Menu item action
     *
     * @var string
     */
    protected $_action = null;

    /**
     * Parent menu item id
     *
     * @var string
     */
    protected $_parentId = null;

    /**
     * Acl resource of menu item
     *
     * @var string
     */
    protected $_resource;

    /**
     * Item tooltip text
     *
     * @var string
     */
    protected $_tooltip;

    /**
     * Path from root element in tree
     *
     * @var string
     */
    protected $_path = '';

    /**
     * Acl
     *
     * @var Mage_Backend_Model_Auth_Session
     */
    protected $_acl;

    /**
     * Module that item is dependent on
     *
     * @var string|null
     */
    protected $_dependsOnModule;

    /**
     * Global config option that item is dependent on
     *
     * @var string|null
     */
    protected $_dependsOnConfig;

    /**
     * Submenu item list
     *
     * @var Mage_Backend_Model_Menu
     */
    protected $_submenu;

    /**
     * @param array $data
     * @throws InvalidArgumentException
     */
    public function __construct(array $data = array())
    {
        if (!isset($data['acl'])
            || !$data['acl'] instanceof Mage_Backend_Model_Auth_Session
            || !isset($data['id']) || !isset($data['title']) || !isset($data['module']) || !isset($data['sortOrder'])
            || !isset($data['action']) || !isset($data['parent'])
        ) {
            throw new InvalidArgumentException();
        }

        $this->_acl = $data['acl'];

        $this->_id = $data['id'];
        $this->_title = $data['title'];
        $this->_moduleHelper = $data['module'];
        $this->_sortIndex = $data['sortOrder'];
        $this->_action = $data['action'];
        $this->_parentId = $data['parent'];
        $this->_resource = isset($data['resource']) ? $data['resource'] : null;
        $this->_dependsOnModule = isset($data['dependsOnModule']) ? $data['dependsOnModule'] : null;
        $this->_dependsOnConfig = isset($data['dependsOnConfig']) ? $data['dependsOnConfig'] : null;
        $this->_tooltip = isset($data['toolTip']) ? $data['toolTip'] : '';

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

    /**
     * Retrieve full path from root element
     *
     * @return string
     */
    public function getFullPath()
    {
        return $this->_path . $this->_id;
    }

    /**
     * Retrieve menu item url
     *
     * @return string
     */
    public function getUrl()
    {
        if ((bool) $this->_action) {
            return $this->_url->getUrl((string)$this->_action, array('_cache_secret_key' => true));
        }
        return '#';
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

    /**
     * Retrieve tooltip text title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Check whether item has tooltip text
     *
     * @return bool
     */
    public function hasTooltip()
    {
        return (bool) $this->_tooltip;
    }

    /**
     * Retrieve item tooltip text
     *
     * @return string
     */
    public function getTooltip()
    {
        return $this->_tooltip;
    }


    /**
     * @return bool
     */
    public function isDisabled()
    {
        if (!$this->_isEnabledModuleOutput() || !$this->_isDependenciesAvailable()) {
            return false;
        }
    }

    /**
     * Check whether module output is enabled
     *
     * @return bool
     */
    protected function _isEnabledModuleOutput()
    {
        return $this->_moduleHelper->isModuleOutputEnabled();
    }

    /**
     * Check Depends
     *
     * @param Varien_Simplexml_Element $depends
     * @return bool
     */
    protected function _isDependenciesAvailable()
    {
        if ($this->_dependsOnModule) {
            $module = $this->_dependsOnModule;
            $modulesConfig = $this->_appConfig->getNode('modules');
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

    /**
     * Check whether item is allowed to the user
     *
     * @return bool
     */
    public function isAllowed()
    {
        try {
            $aclResource = 'admin/' . ($this->_resource ? (string)$this->_resource : $this->getFullPath());
            return $this->_acl->isAllowed($aclResource);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Add child to submenu
     *
     * @param Mage_Backend_Model_Menu_Item $item
     */
    public function addChild(Mage_Backend_Model_Menu_Item $item)
    {
        if (!$this->_submenu) {
            $this->_submenu = $this->_objectFactory->getModelInstance('Mage_Backend_Model_Menu');
        }
        $this->_submenu->addChild($item);
    }

    /**
     * Add item to tree structure
     *
     * @param Mage_Backend_Model_Menu $menu
     */
    public function setParent(Mage_Backend_Model_Menu $menu)
    {
        $this->_path = $menu->getFullPath();
    }

}
