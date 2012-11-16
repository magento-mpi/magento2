<?php
/**
 * Role item model
 *
 * @copyright {}
 *
 * @method int getRoleId()
 * @method string getRoleName()
 * @method Mage_Webapi_Model_Acl_Role setRoleName(string $value)
 */
class Mage_Webapi_Model_Acl_Role extends Mage_Core_Model_Abstract
{
    /**
     * @var Magento_Acl
     */
    protected $_aclModel;

    /**
     * @var Mage_Webapi_Model_Authorization_Loader_Resource
     */
    protected $_authLoader;

    /**
     * @var Mage_Webapi_Model_Authorization_Config
     */
    protected $_authConfig;

    /**
     * Constructor
     *
     * @param Mage_Core_Model_Event_Manager $eventDispatcher
     * @param Mage_Core_Model_Cache $cacheManager
     * @param Mage_Core_Model_Resource_Abstract $resource
     * @param Varien_Data_Collection_Db $resourceCollection
     * @param Magento_Acl $aclModel
     * @param Mage_Webapi_Model_Authorization_Loader_Resource $authLoader
     * @param Mage_Webapi_Model_Authorization_Config $authConfig
     * @param array $data
     */
    public function __construct(Mage_Core_Model_Event_Manager $eventDispatcher,
        Mage_Core_Model_Cache $cacheManager,
        Magento_Acl $aclModel,
        Mage_Webapi_Model_Authorization_Loader_Resource $authLoader,
        Mage_Webapi_Model_Authorization_Config $authConfig,
        Mage_Core_Model_Resource_Abstract $resource = null,
        Varien_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_aclModel = $aclModel;
        $this->_authLoader = $authLoader;
        $this->_authConfig = $authConfig;

        parent::__construct($eventDispatcher, $cacheManager, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource
     */
    protected function _construct()
    {
        $this->_init('Mage_Webapi_Model_Resource_Acl_Role');
    }
}
