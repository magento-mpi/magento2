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
     * @param Magento_ObjectManager $factory
     * @param Mage_Backend_Model_Auth_Session $authorization
     * @param Mage_Backend_Model_Menu_Factory $menuFactory
     * @param Mage_Core_Model_Config $appliactionConfig
     * @param Mage_Core_Model_Store_Config $storeConfig
     * @param Mage_Backend_Model_Url $urlModel
     * @param Mage_Backend_Model_Menu_Item_Validator $menuItemValidator
     * @param array $data
     */
    public function __construct(
        Magento_ObjectManager $factory,
        Mage_Backend_Model_Auth_Session $authorization,
        Mage_Backend_Model_Menu_Factory $menuFactory,
        Mage_Core_Model_Config $appliactionConfig,
        Mage_Core_Model_Store_Config $storeConfig,
        Mage_Backend_Model_Url $urlModel,
        Mage_Backend_Model_Menu_Item_Validator $menuItemValidator,
        array $data = array())
    {
        $this->_acl = $authorization;
        $this->_objectFactory = $factory;
        $this->_menuFactory = $menuFactory;
        $this->_appConfig = $appliactionConfig;
        $this->_storeConfig = $storeConfig;
        $this->_urlModel = $urlModel;
        $this->_validator = $menuItemValidator;

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
            unset($data['module']);
        }

        $data = array('data' => $data);

        $data['authorization'] = $this->_acl;
        $data['applicationConfig'] = $this->_appConfig;
        $data['storeConfig'] = $this->_storeConfig;
        $data['menuFactory'] = $this->_menuFactory;
        $data['urlModel'] = $this->_urlModel;
        $data['validator'] = $this->_validator;
        $data['helper'] = isset($this->_helpers[$module]) ? $this->_helpers[$module] : Mage::helper($module);
        return $this->_objectFactory->create('Mage_Backend_Model_Menu_Item', $data);
    }
}
