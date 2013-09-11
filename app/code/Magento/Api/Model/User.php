<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Api
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @method \Magento\Api\Model\Resource\User _getResource()
 * @method \Magento\Api\Model\Resource\User getResource()
 * @method string getFirstname()
 * @method \Magento\Api\Model\User setFirstname(string $value)
 * @method string getLastname()
 * @method \Magento\Api\Model\User setLastname(string $value)
 * @method string getEmail()
 * @method \Magento\Api\Model\User setEmail(string $value)
 * @method string getUsername()
 * @method \Magento\Api\Model\User setUsername(string $value)
 * @method string getApiKey()
 * @method \Magento\Api\Model\User setApiKey(string $value)
 * @method string getCreated()
 * @method \Magento\Api\Model\User setCreated(string $value)
 * @method string getModified()
 * @method \Magento\Api\Model\User setModified(string $value)
 * @method int getLognum()
 * @method \Magento\Api\Model\User setLognum(int $value)
 * @method int getReloadAclFlag()
 * @method \Magento\Api\Model\User setReloadAclFlag(int $value)
 * @method int getIsActive()
 * @method \Magento\Api\Model\User setIsActive(int $value)
 *
 * @category    Magento
 * @package     Magento_Api
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Api\Model;

class User extends \Magento\Core\Model\AbstractModel
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'api_user';

    protected function _construct()
    {
        $this->_init('Magento\Api\Model\Resource\User');
    }

    /**
     * @return $this|\Magento\Core\Model\AbstractModel
     */
    public function save()
    {
        $this->_beforeSave();
        $data = array(
                'firstname' => $this->getFirstname(),
                'lastname'  => $this->getLastname(),
                'email'     => $this->getEmail(),
                'modified'  => \Mage::getSingleton('Magento\Core\Model\Date')->gmtDate()
            );

        if ($this->getId() > 0) {
            $data['user_id']   = $this->getId();
        }

        if ($this->getUsername()) {
            $data['username']   = $this->getUsername();
        }

        if ($this->getApiKey()) {
            $data['api_key']   = $this->_getEncodedApiKey($this->getApiKey());
        }

        if ($this->getNewApiKey()) {
            $data['api_key']   = $this->_getEncodedApiKey($this->getNewApiKey());
        }

        if ( !is_null($this->getIsActive()) ) {
            $data['is_active']  = intval($this->getIsActive());
        }

        $this->setData($data);
        $this->_getResource()->save($this);
        $this->_afterSave();
        return $this;
    }

    /**
     * @return $this|\Magento\Core\Model\AbstractModel
     */
    public function delete()
    {
        $this->_beforeDelete();
        $this->_getResource()->delete($this);
        $this->_afterDelete();
        return $this;
    }

    /**
     * @return $this
     */
    public function saveRelations()
    {
        $this->_getResource()->_saveRelations($this);
        return $this;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->_getResource()->_getRoles($this);
    }

    /**
     * @return $this
     */
    public function deleteFromRole()
    {
        $this->_getResource()->deleteFromRole($this);
        return $this;
    }

    /**
     * @return bool
     */
    public function roleUserExists()
    {
        $result = $this->_getResource()->roleUserExists($this);
        return (is_array($result) && count($result) > 0) ? true : false;
    }

    /**
     * @return $this
     */
    public function add()
    {
        $this->_getResource()->add($this);
        return $this;
    }

    /**
     * @return bool
     */
    public function userExists()
    {
        $result = $this->_getResource()->userExists($this);
        return is_array($result) && count($result) > 0;
    }

    /**
     * @return \Magento\Core\Model\Resource\Db\Collection\AbstractCollection|\Magento\Api\Model\Resource\User\Collection
     */
    public function getCollection()
    {
        return \Mage::getResourceModel('Magento\Api\Model\Resource\User\Collection');
    }

    /**
     * @param string $separator
     * @return string
     */
    public function getName($separator = ' ')
    {
        return $this->getFirstname() . $separator . $this->getLastname();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->getUserId();
    }

    /**
     * Get user ACL role
     *
     * @return string
     */
    public function getAclRole()
    {
        return 'U' . $this->getUserId();
    }

    /**
     * Authenticate user name and api key and save loaded record
     *
     * @param string $username
     * @param string $apiKey
     * @return boolean
     */
    public function authenticate($username, $apiKey)
    {
        $this->loadByUsername($username);
        if (!$this->getId()) {
            return false;
        }
        $auth = \Mage::helper('Magento\Core\Helper\Data')->validateHash($apiKey, $this->getApiKey());
        if ($auth) {
            return true;
        } else {
            $this->unsetData();
            return false;
        }
    }

    /**
     * Login user
     *
     * @param   string $username
     * @param   string $apiKey
     * @return  \Magento\Api\Model\User
     */
    public function login($username, $apiKey)
    {
        $sessId = $this->getSessid();
        if ($this->authenticate($username, $apiKey)) {
            $this->setSessid($sessId);
            $this->getResource()->cleanOldSessions($this)
                ->recordLogin($this)
                ->recordSession($this);
            \Mage::dispatchEvent('api_user_authenticated', array(
               'model'    => $this,
               'api_key'  => $apiKey,
            ));
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function reload()
    {
        $this->load($this->getId());
        return $this;
    }

    /**
     * @param string $username
     * @return $this
     */
    public function loadByUsername($username)
    {
        $this->setData($this->getResource()->loadByUsername($username));
        return $this;
    }

    /**
     * @param string $sessId
     * @return $this
     */
    public function loadBySessId($sessId)
    {
        $this->setData($this->getResource()->loadBySessId($sessId));
        return $this;
    }

    /**
     * @param string $sessid
     * @return $this
     */
    public function logoutBySessId($sessid)
    {
        $this->getResource()->clearBySessId($sessid);
        return $this;
    }

    /**
     * @param int|\Magento\Api\Model\User $user
     * @return array|null
     */
    public function hasAssigned2Role($user)
    {
        return $this->getResource()->hasAssigned2Role($user);
    }

    /**
     * @param string $apiKey
     * @return mixed
     */
    protected function _getEncodedApiKey($apiKey)
    {
        return \Mage::helper('Magento\Core\Helper\Data')->getHash($apiKey, 2);
    }

}
