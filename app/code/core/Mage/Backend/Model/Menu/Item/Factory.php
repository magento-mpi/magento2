<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Model_Menu_Item_Factory
{
    /**
     * ACL
     *
     * @var Mage_Backend_Model_Auth_Session
     */
    protected $_acl;

    /**
     * Object factory
     *
     * @var Mage_Core_Model_Config
     */
    protected $_objectFactory;

    /**
     * @var Mage_Backend_Model_Menu_Factory
     */
    protected $_menuFactory;

    /**
     * @var Mage_Core_Helper_Abstract[]
     */
    protected $_helpers = array();

    /**
     * @var Mage_Backend_Model_Url
     */
    protected $_urlModel;

    /**
     * Application Configuration
     *
     * @var Mage_Core_Model_Config
     */
    protected $_appConfig;

    /**
     * Store Configuration
     *
     * @var Mage_Core_Model_Store_Config
     */
    protected $_storeConfig;

    /**
     * Menu item parameter validator
     *
     * @var Mage_Backend_Model_Menu_Item_Validator
     */
    protected $_validator;

    /**
     * @param array $data
     * @throws InvalidArgumentException
     */
    public function __construct(array $data = array())
    {
        $this->_acl = isset($data['acl']) ? $data['acl'] : Mage::getSingleton('Mage_Backend_Model_Auth_Session');
        if (!($this->_acl instanceof Mage_Backend_Model_Auth_Session)) {
            throw new InvalidArgumentException('Wrong acl object provided');
        }

        $this->_menuFactory = isset($data['menuFactory']) ? $data['menuFactory'] : new Mage_Backend_Model_Menu_Factory();
        if (!($this->_menuFactory instanceof Mage_Backend_Model_Menu_Factory)) {
            throw new InvalidArgumentException('Wrong menu factory provided');
        }

        $this->_appConfig = isset($data['appConfig']) ? $data['appConfig']: Mage::getConfig();
        if (!($this->_appConfig instanceof Mage_Core_Model_Config)) {
            throw new InvalidArgumentException('Wrong application config provided');
        }

        $this->_storeConfig = isset($data['storeConfig'])
            ? $data['storeConfig']
            : Mage::getSingleton('Mage_Core_Model_Store_Config');
        if (!($this->_storeConfig instanceof Mage_Core_Model_Store_Config)) {
            throw new InvalidArgumentException('Wrong store config provided');
        }

        $this->_urlModel = isset($data['urlModel']) ? $data['urlModel'] : Mage::getSingleton('Mage_Backend_Model_Url');
        if (!($this->_urlModel instanceof Mage_Backend_Model_Url)) {
            throw new InvalidArgumentException('Wrong url model provided');
        }

        $this->_validator = isset($data['validator'])
            ? $data['validator']
            : Mage::getSingleton('Mage_Backend_Model_Menu_Item_Validator');
        if (!($this->_validator instanceof Mage_Backend_Model_Menu_Item_Validator)) {
            throw new InvalidArgumentException('Wrong item validator model provided');
        }

        if (isset($data['helpers'])) {
            $this->_helpers = $data['helpers'];
        }
    }

    /**
     * Create menu item from array
     *
     * @param array $data
     * @return Mage_Backend_Model_Menu_Item
     */
    public function createFromArray(array $data = array())
    {
        $module = 'Mage_Backend_Helper_Data';
        if (isset($data['module'])) {
            $module = $data['module'];
        }

        $data['module'] = isset($this->_helpers[$module]) ? $this->_helpers[$module] : Mage::helper($module);
        $data['acl'] = $this->_acl;
        $data['appConfig'] = $this->_appConfig;
        $data['storeConfig'] = $this->_storeConfig;
        $data['menuFactory'] = $this->_menuFactory;
        $data['urlModel'] = $this->_urlModel;
        $data['validator'] = $this->_validator;
        return $this->_menuFactory->getMenuItemInstance($data);
    }
}
