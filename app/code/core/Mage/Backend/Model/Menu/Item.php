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
     * Menu item sort index in list
     *
     * @var string
     */
    protected $_sortIndex = null;

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

    public function getUrl()
    {
        if ($child->action) {
            return $this->_url->getUrl((string)$child->action, array('_cache_secret_key' => true));
        }
        $this->_clickCallback = 'return false';
        return '#';
    }

    public function hasTitle()
    {

    }

    public function getTitle()
    {

    }

    public function hasClickCallback()
    {

    }

    public function getClickCallback()
    {

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
        if ($depends->module) {
            $modulesConfig = Mage::getConfig()->getNode('modules');
            foreach ($depends->module as $module) {
                if (!$modulesConfig->$module || !$modulesConfig->$module->is('active')) {
                    return false;
                }
            }
        }

        if ($depends->config) {
            foreach ($depends->config as $path) {
                if (!Mage::getStoreConfigFlag((string)$path)) {
                    return false;
                }
            }
        }

        return true;
    }


    public function isAllowed()
    {
        $aclResource = 'admin/' . ($child->resource ? (string)$child->resource : $path . $childName);
        if (!$this->_checkAcl($aclResource)){

        }

    }

    public function addChild(Mage_Backend_Model_Menu_Item $item)
    {
        if (!$this->_submenu) {
            $this->_submenu = new Mage_Backend_Model_Menu();
        }
        $this->_submenu->addChild($item);
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
