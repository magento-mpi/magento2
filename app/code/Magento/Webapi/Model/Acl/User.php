<?php
/**
 * Web API User model.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @method Magento_Webapi_Model_Acl_User setRoleId() setRoleId(int $id)
 * @method int getRoleId() getRoleId()
 * @method Magento_Webapi_Model_Acl_User setApiKey() setApiKey(string $apiKey)
 * @method string getApiKey() getApiKey()
 * @method Magento_Webapi_Model_Acl_User setContactEmail() setContactEmail(string $contactEmail)
 * @method string getContactEmail() getContactEmail()
 * @method Magento_Webapi_Model_Acl_User setSecret() setSecret(string $secret)
 * @method Magento_Webapi_Model_Acl_User setCompanyName() setCompanyName(string $companyName)
 * @method string getCompanyName() getCompanyName()
 */
class Magento_Webapi_Model_Acl_User extends Magento_Core_Model_Abstract implements Magento_Oauth_Model_ConsumerInterface
{
    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'webapi_user';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('Magento_Webapi_Model_Resource_Acl_User');
    }

    /**
     * Get role users.
     *
     * @param integer $roleId
     * @return array
     */
    public function getRoleUsers($roleId)
    {
        return $this->getResource()->getRoleUsers($roleId);
    }

    /**
     * Load user by key.
     *
     * @param string $key
     * @return Magento_Webapi_Model_Acl_User
     */
    public function loadByKey($key)
    {
        return $this->load($key, 'api_key');
    }

    /**
     * Get consumer key.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->getData('key');
    }

    /**
     * Get consumer secret.
     *
     * @return string
     */
    public function getSecret()
    {
        return $this->getData('secret');
    }

    /**
     * Get consumer callback URL.
     *
     * @return string
     */
    public function getCallBackUrl()
    {
         return '';
    }
}
