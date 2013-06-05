<?php
/**
 * Limitation of number of users in the system
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_User_Limitation
{
    /**
     * XPath to configuration node with specified limitation for number of users
     */
    const XML_PATH_NUM_USERS = 'limitations/admin_account';

    /**
     * @var Mage_User_Model_Resource_User
     */
    private $_resource;

    /**
     * @var Mage_Core_Model_Config
     */
    private $_config;

    /**
     * @param Mage_User_Model_Resource_User $resource
     * @param Mage_Core_Model_Config $config
     */
    public function __construct(Mage_User_Model_Resource_User $resource, Mage_Core_Model_Config $config)
    {
        $this->_resource = $resource;
        $this->_config = $config;
    }

    /**
     * Check if creation of the entity is restricted
     *
     * @return bool
     */
    public function isCreateRestricted()
    {
        $limit = (int)$this->_config->getNode(self::XML_PATH_NUM_USERS);
        if ($limit > 0) {
            return $this->_resource->countAll() >= $limit;
        }
        return false;
    }

    /**
     * Get restriction message
     *
     * @return string
     */
    public function getCreateRestrictedMessage()
    {
        // @codingStandardsIgnoreStart
        return Mage::helper('Saas_Limitation_Helper_Data')->__('Sorry, you are using all the admin users your account allows. To add more, first delete an admin user or upgrade your service.');
        // @codingStandardsIgnoreEnd
    }
}
