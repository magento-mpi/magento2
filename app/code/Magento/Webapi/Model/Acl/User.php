<?php
/**
 * Web API User model.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @method \Magento\Webapi\Model\Acl\User setRoleId() setRoleId(int $id)
 * @method int getRoleId() getRoleId()
 * @method \Magento\Webapi\Model\Acl\User setApiKey() setApiKey(string $apiKey)
 * @method string getApiKey() getApiKey()
 * @method \Magento\Webapi\Model\Acl\User setContactEmail() setContactEmail(string $contactEmail)
 * @method string getContactEmail() getContactEmail()
 * @method \Magento\Webapi\Model\Acl\User setSecret() setSecret(string $secret)
 * @method \Magento\Webapi\Model\Acl\User setCompanyName() setCompanyName(string $companyName)
 * @method string getCompanyName() getCompanyName()
 */
namespace Magento\Webapi\Model\Acl;

class User extends \Magento\Core\Model\AbstractModel implements \Magento\Oauth\Model\ConsumerInterface
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
        $this->_init('Magento\Webapi\Model\Resource\Acl\User');
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
     * @return \Magento\Webapi\Model\Acl\User
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
