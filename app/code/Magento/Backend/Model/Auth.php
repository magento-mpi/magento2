<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend Auth model
 */
class Magento_Backend_Model_Auth
{
    /**
     * @var Magento_Backend_Model_Auth_StorageInterface
     */
    protected $_authStorage;

    /**
     * @var Magento_Backend_Model_Auth_Credential_StorageInterface
     */
    protected $_credentialStorage;

    /**
     * Backend data
     *
     * @var Magento_Backend_Helper_Data
     */
    protected $_backendData;

    /**
     * Core event manager proxy
     *
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager;

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_coreConfig;

    /**
     * @var Magento_Core_Model_Factory
     */
    protected $_modelFactory;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Backend_Helper_Data $backendData
     * @param Magento_Backend_Model_Auth_StorageInterface $authStorage
     * @param Magento_Backend_Model_Auth_Credential_StorageInterface $credentialStorage
     * @param Magento_Core_Model_Config $coreConfig
     * @param Magento_Core_Model_Factory $modelFactory
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Backend_Helper_Data $backendData,
        Magento_Backend_Model_Auth_StorageInterface $authStorage,
        Magento_Backend_Model_Auth_Credential_StorageInterface $credentialStorage,
        Magento_Core_Model_Config $coreConfig,
        Magento_Core_Model_Factory $modelFactory
    ) {
        $this->_eventManager = $eventManager;
        $this->_backendData = $backendData;
        $this->_authStorage = $authStorage;
        $this->_credentialStorage = $credentialStorage;
        $this->_coreConfig = $coreConfig;
        $this->_modelFactory = $modelFactory;
    }

    /**
     * Set auth storage if it is instance of Magento_Backend_Model_Auth_StorageInterface
     *
     * @param Magento_Backend_Model_Auth_StorageInterface $storage
     * @return Magento_Backend_Model_Auth
     * @throw Magento_Backend_Model_Auth_Exception if $storage is not correct
     */
    public function setAuthStorage($storage)
    {
        if (!($storage instanceof Magento_Backend_Model_Auth_StorageInterface)) {
            self::throwException('Authentication storage is incorrect.');
        }
        $this->_authStorage = $storage;
        return $this;
    }

    /**
     * Return auth storage.
     * If auth storage was not defined outside - returns default object of auth storage
     *
     * @return Magento_Backend_Model_Auth_StorageInterface
     */
    public function getAuthStorage()
    {
        return $this->_authStorage;
    }

    /**
     * Return current (successfully authenticated) user,
     * an instance of Magento_Backend_Model_Auth_Credential_StorageInterface
     *
     * @return Magento_Backend_Model_Auth_Credential_StorageInterface
     */
    public function getUser()
    {
        return $this->getAuthStorage()->getUser();
    }

    /**
     * Initialize credential storage from configuration
     *
     * @return void
     * @throw Magento_Backend_Model_Auth_Exception if credential storage absent or has not correct configuration
     */
    protected function _initCredentialStorage()
    {
        $areaConfig = $this->_coreConfig->getAreaConfig($this->_backendData->getAreaCode());

        if (isset($areaConfig['auth_credential_storage'])) {
            $storage = $this->_modelFactory->create($areaConfig['auth_credential_storage']);
            if ($storage instanceof Magento_Backend_Model_Auth_Credential_StorageInterface) {
                $this->_credentialStorage = $storage;
                return;
            }
        }
        self::throwException(
            __('There are no authentication credential storage.')
        );
    }

    /**
     * Return credential storage object
     *
     * @return null | Magento_Backend_Model_Auth_Credential_StorageInterface
     */
    public function getCredentialStorage()
    {
        return $this->_credentialStorage;
    }

    /**
     * Perform login process
     *
     * @param string $username
     * @param string $password
     * @throws Exception|Magento_Backend_Model_Auth_Plugin_Exception
     */
    public function login($username, $password)
    {
        if (empty($username) || empty($password)) {
            self::throwException(
                __('Please correct the user name or password.')
            );
        }

        try {
            $this->_initCredentialStorage();
            $this->getCredentialStorage()->login($username, $password);
            if ($this->getCredentialStorage()->getId()) {

                $this->getAuthStorage()->setUser($this->getCredentialStorage());
                $this->getAuthStorage()->processLogin();

                $this->_eventManager
                    ->dispatch('backend_auth_user_login_success', array('user' => $this->getCredentialStorage()));
            }

            if (!$this->getAuthStorage()->getUser()) {
                self::throwException(
                    __('Please correct the user name or password.')
                );
            }

        } catch (Magento_Backend_Model_Auth_Plugin_Exception $e) {
            $this->_eventManager
                ->dispatch('backend_auth_user_login_failed', array('user_name' => $username, 'exception' => $e));
            throw $e;
        } catch (Magento_Core_Exception $e) {
            $this->_eventManager
                ->dispatch('backend_auth_user_login_failed', array('user_name' => $username, 'exception' => $e));
            self::throwException(
                __('Please correct the user name or password.')
            );
        }
    }

    /**
     * Perform logout process
     *
     * @return void
     */
    public function logout()
    {
        $this->getAuthStorage()->processLogout();
    }

    /**
     * Check if current user is logged in
     *
     * @return boolean
     */
    public function isLoggedIn()
    {
        return $this->getAuthStorage()->isLoggedIn();
    }

    /**
     * Throws specific Backend Authentication Exception
     *
     * @static
     * @param string $msg
     * @param string $code
     * @throws Magento_Backend_Model_Auth_Exception
     */
    public static function throwException($msg = null, $code = null)
    {
        if (is_null($msg)) {
            $msg = __('Authentication error occurred.');
        }
        throw new Magento_Backend_Model_Auth_Exception($msg, $code);
    }
}
