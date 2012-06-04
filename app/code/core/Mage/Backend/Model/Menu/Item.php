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
 * Menu item. Should be used to create nested menu structures with Mage_Backend_Model_Menu
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
     * @var Mage_Core_Helper_Abstract
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
     * @var Mage_Core_Model_Config
     */
    protected $_appConfig;

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_objectFactory;

    /**
     * @var Mage_Backend_Model_Url
     */
    protected $_urlModel;

    /**
     * @var Mage_Core_Model_Store_Config
     */
    protected $_storeConfig;

    /**
     * @param array $data
     * @throws InvalidArgumentException
     */
    public function __construct(array $data = array())
    {
        if (!isset($data['acl'])
            || !$data['acl'] instanceof Mage_Backend_Model_Auth_Session
        ) {
            throw new InvalidArgumentException('Wrong acl object provided');
        }

        if (!isset($data['appConfig'])
            || !$data['appConfig'] instanceof Mage_Core_Model_Config
        ) {
            throw new InvalidArgumentException('Wrong application config provided');
        }

        if (!isset($data['objectFactory'])
            || !$data['objectFactory'] instanceof Mage_Core_Model_Config
        ) {
            throw new InvalidArgumentException('Wrong object factory provided');
        }

        if (!isset($data['urlModel'])
            || !$data['urlModel'] instanceof Mage_Backend_Model_Url
        ) {
            throw new InvalidArgumentException('Wrong url model provided');
        }

        if (!isset($data['storeConfig'])
            || !$data['storeConfig'] instanceof Mage_Core_Model_Store_Config
        ) {
            throw new InvalidArgumentException('Wrong store config provided');
        }

        $this->_acl = $data['acl'];
        $this->_appConfig = $data['appConfig'];
        $this->_storeConfig = $data['storeConfig'];
        $this->_objectFactory = $data['objectFactory'];
        $this->_urlModel = $data['urlModel'];


        $this->_id = $data['id'];
        $this->_title = $data['title'];
        $this->_moduleHelper = $data['module'];
        $this->_parentId = $data['parent'];
        $this->_sortIndex = isset($data['sortOrder']) ? $data['sortOrder'] : null;
        $this->_action = isset($data['action']) ? $data['action'] : null;
        $this->_resource = isset($data['resource']) ? $data['resource'] : null;
        $this->_dependsOnModule = isset($data['dependsOnModule']) ? $data['dependsOnModule'] : null;
        $this->_dependsOnConfig = isset($data['dependsOnConfig']) ? $data['dependsOnConfig'] : null;
        $this->_tooltip = isset($data['toolTip']) ? $data['toolTip'] : '';
    }

    /**
     * Check if item has sort index that is used to sort items in menu
     *
     * @return bool
     */
    public function hasSortIndex()
    {
        return (bool) $this->_sortIndex;
    }

    /**
     * Retrieve sort index that is used to sort items in menu
     *
     * @return int
     */
    public function getSortIndex()
    {
        return $this->_sortIndex;
    }

    /**
     * Retrieve item id
     *
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Check whether menu item has parent node id
     *
     * @return bool
     */
    public function hasParentId()
    {
        return (bool) $this->_parentId;
    }

    /**
     * Retrieve parent node id
     *
     * @return null|string
     */
    public function getParentId()
    {
        return $this->_parentId;
    }

    /**
     * Check whether item has subnodes
     *
     * @return bool
     */
    public function hasChildren()
    {
        return !is_null($this->_submenu) && (bool) $this->_submenu->count();
    }

    /**
     * Retrieve submenu
     *
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
            return $this->_urlModel->getUrl((string)$this->_action, array('_cache_secret_key' => true));
        }
        return '#';
    }

    /**
     * Retrieve menu item action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->_action;
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
     * Retrieve module helper object linked to item.
     * Should be used to translate item labels
     *
     * @return Mage_Core_Helper_Abstract
     */
    public function getModuleHelper()
    {
        return $this->_moduleHelper;
    }

    /**
     * Check whether item is disabled. Disabled items are not shown to user
     *
     * @return bool
     */
    public function isDisabled()
    {
        return !$this->_moduleHelper->isModuleOutputEnabled()
            || !$this->_isModuleDependenciesAvailable()
            || !$this->_isConfigDependenciesAvailable();
    }

    /**
     * Check whether module that item depends on is active
     *
     * @return bool
     */
    protected function _isModuleDependenciesAvailable()
    {
        if ($this->_dependsOnModule) {
            $module = $this->_dependsOnModule;
            $modulesConfig = $this->_appConfig->getNode('modules');
            return ($modulesConfig->$module && $modulesConfig->$module->is('active'));
        }
        return true;
    }

    /**
     * Check whether config dependency is available
     *
     * @return bool
     */
    protected function _isConfigDependenciesAvailable()
    {
        if ($this->_dependsOnConfig) {
            return $this->_storeConfig->getConfigFlag((string)$this->_dependsOnConfig);
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
            $this->_submenu = $this->_objectFactory->getModelInstance(
                'Mage_Backend_Model_Menu',
                array('path' => $this->getFullPath())
            );
        }
        $this->_submenu->addChild($item);
    }

    /**
     * Set parent node in tree structure
     *
     * @param Mage_Backend_Model_Menu $menu
     */
    public function setParent(Mage_Backend_Model_Menu $menu)
    {
        $this->_path = $menu->getFullPath();
        if ($this->_submenu) {
            $this->_submenu->setPath($this->getFullPath());
        }
    }

    /**
     * Retrieve first allowed to user leaf menu item action
     *
     * @return string
     */
    public function getFirstAvailableChild()
    {
        $action = null;
        if ($this->_submenu) {
            $action = $this->_submenu->getFirstAvailableChild();
        }
        return $action ? $action : $this->_action;
    }
}
