<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Web API User model
 *
 * @method Mage_Webapi_Model_Acl_User setRoleId(int $id)
 * @method int getRoleId()
 * @method Mage_Webapi_Model_Acl_User setApiKey(string $apiKey)
 * @method string getApiKey()
 * @method Mage_Webapi_Model_Acl_User setContactEmail(string $contactEmail)
 * @method string getContactEmail()
 * @method Mage_Webapi_Model_Acl_User setCompanyName(string $companyName)
 * @method string getCompanyName()
 * @method Mage_Webapi_Model_Resource_Acl_User getResource()
 *
 * @category   Mage
 * @package    Mage_Webapi
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Model_Acl_User extends Mage_Core_Model_Abstract implements Mage_Oauth_Model_ConsumerInterface
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'webapi_user';

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('Mage_Webapi_Model_Resource_Acl_User');
    }

    /**
     * Get role users
     *
     * @param int $roleId
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
     * @return Mage_Webapi_Model_Acl_User
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
