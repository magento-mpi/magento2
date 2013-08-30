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
 * @method Magento_Api_Model_Resource_User _getResource()
 * @method Magento_Api_Model_Resource_User getResource()
 * @method string getFirstname()
 * @method Magento_Api_Model_User setFirstname(string $value)
 * @method string getLastname()
 * @method Magento_Api_Model_User setLastname(string $value)
 * @method string getEmail()
 * @method Magento_Api_Model_User setEmail(string $value)
 * @method string getUsername()
 * @method Magento_Api_Model_User setUsername(string $value)
 * @method string getApiKey()
 * @method Magento_Api_Model_User setApiKey(string $value)
 * @method string getCreated()
 * @method Magento_Api_Model_User setCreated(string $value)
 * @method string getModified()
 * @method Magento_Api_Model_User setModified(string $value)
 * @method int getLognum()
 * @method Magento_Api_Model_User setLognum(int $value)
 * @method int getReloadAclFlag()
 * @method Magento_Api_Model_User setReloadAclFlag(int $value)
 * @method int getIsActive()
 * @method Magento_Api_Model_User setIsActive(int $value)
 *
 * @category    Magento
 * @package     Magento_Api
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Api_Model_User extends Magento_Core_Model_Abstract
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'api_user';

    protected function _construct()
    {
        $this->_init('Magento_Api_Model_Resource_User');
    }

    /**
     * @return $this|Magento_Core_Model_Abstract
     */
    public function save()
    {
        $this->_beforeSave();
        $data = array(
                'firstname' => $this->getFirstname(),
                'lastname'  => $this->getLastname(),
                'email'     => $this->getEmail(),
                'modified'  => Mage::getSingleton('Magento_Core_Model_Date')->gmtDate()
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
     * @return $this|Magento_Core_Model_Abstract
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
     * @return Magento_Core_Model_Resource_Db_Collection_Abstract|Magento_Api_Model_Resource_User_Collection
     */
    public function getCollection()
    {
        return Mage::getResourceModel('Magento_Api_Model_Resource_User_Collection');
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
        $auth = Mage::helper('Magento_Core_Helper_Data')->validateHash($apiKey, $this->getApiKey());
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
     * @return  Magento_Api_Model_User
     */
    public function login($username, $apiKey)
    {
        $sessId = $this->getSessid();
        if ($this->authenticate($username, $apiKey)) {
            $this->setSessid($sessId);
            $this->getResource()->cleanOldSessions($this)
                ->recordLogin($this)
                ->recordSession($this);
            Mage::dispatchEvent('api_user_authenticated', array(
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
     * @param int|Magento_Api_Model_User $user
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
        return Mage::helper('Magento_Core_Helper_Data')->getHash($apiKey, 2);
    }

}
