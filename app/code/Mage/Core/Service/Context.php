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

    public function __construct(
        Mage_Core_Model_Acl_Builder $aclBuilder,
        Mage_Core_Model_StoreManager $storeManager)
    {
        $this->_aclBuilder = $aclBuilder;

        $this->_storeManager = $storeManager;
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
