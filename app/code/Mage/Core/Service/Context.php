<?php

class Mage_Core_Service_Context
{
    const AREA_SERVICES = 'services';

    /**
     * Access Control List builder
     *
     * @var Mage_Core_Model_Acl_Builder
     */
    protected $_aclBuilder;

    /**
     * @var Mage_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    public function __construct(
        Mage_Core_Model_Acl_Builder $aclBuilder,
        Mage_Core_Model_StoreManager $storeManager,
        Mage_Core_Model_Config $config)
    {
        $this->_aclBuilder = $aclBuilder;

        $this->_storeManager = $storeManager;

        $this->_config = $config;
    }

    /**
     * @return Mage_Core_Model_Config
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * @return Magento_Acl
     */
    public function getAcl()
    {
        $acl = $this->_aclBuilder->getAcl(self::AREA_SERVICES);
        return $acl;
    }

    /**
     * @return Mage_User_Model_User
     */
    public function getUser()
    {
        // @toto remove stub
        $user = Mage::getSingleton('Mage_User_Model_User')->setUserId(1);
        return $user;
    }

    /**
     * @return Mage_User_Model_User
     */
    public function getStore()
    {
        return $this->_storeManager->getCurrentStore();
    }

    public function getArea()
    {
        return Mage::getConfig()->getCurrentAreaCode();
    }
}
