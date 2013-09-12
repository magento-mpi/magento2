<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Cms Hierarchy Pages Lock Model
 *
 * @method Magento_VersionsCms_Model_Resource_Hierarchy_Lock _getResource()
 * @method Magento_VersionsCms_Model_Resource_Hierarchy_Lock getResource()
 * @method int getUserId()
 * @method Magento_VersionsCms_Model_Hierarchy_Lock setUserId(int $value)
 * @method string getUserName()
 * @method Magento_VersionsCms_Model_Hierarchy_Lock setUserName(string $value)
 * @method string getSessionId()
 * @method Magento_VersionsCms_Model_Hierarchy_Lock setSessionId(string $value)
 * @method int getStartedAt()
 * @method Magento_VersionsCms_Model_Hierarchy_Lock setStartedAt(int $value)
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

/**
 * @deprecated since 1.12.0.0
 */
class Magento_VersionsCms_Model_Hierarchy_Lock extends Magento_Core_Model_Abstract
{
    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Session model instance
     *
     * @var Magento_Backend_Model_Auth_Session
     */
    protected $_session;

    /**
     * Flag indicating whether lock data loaded or not
     *
     * @var bool
     */
    protected $_dataLoaded = false;

    /**
     * Resource model initializing
     */
    protected function _construct()
    {
        $this->_init('Magento_VersionsCms_Model_Resource_Hierarchy_Lock');
    }

    /**
     * Setter for session instance
     *
     * @param Magento_Core_Model_Session_Abstract $session
     * @return Magento_VersionsCms_Model_Hierarchy_Lock
     */
    public function setSession(Magento_Core_Model_Session_Abstract $session)
    {
        $this->_session = $session;
        return $this;
    }

    /**
     * Getter for session instance
     *
     * @return Magento_Core_Model_Session_Abstract
     */
    protected function _getSession()
    {
        if ($this->_session === null) {
            return Mage::getSingleton('Magento_Backend_Model_Auth_Session');
        }
        return $this->_session;
    }

    /**
     * Load lock data
     *
     * @return Magento_VersionsCms_Model_Hierarchy_Lock
     */
    public function loadLockData()
    {
        if (!$this->_dataLoaded) {
            $data = $this->_getResource()->getLockData();
            $this->addData($data);
            $this->_dataLoaded = true;
        }
        return $this;
    }

    /**
     * Check whether page is locked for current user
     *
     * @return bool
     */
    public function isLocked()
    {
        return ($this->isEnabled() && $this->isActual());
    }

    /**
     * Check whether lock belongs to current user
     *
     * @return bool
     */
    public function isLockedByMe()
    {
        return ($this->isLocked() && $this->isLockOwner());
    }

    /**
     * Check whether lock belongs to other user
     *
     * @return bool
     */
    public function isLockedByOther()
    {
        return ($this->isLocked() && !$this->isLockOwner());
    }

    /**
     * Revalidate lock data
     *
     * @return Magento_VersionsCms_Model_Hierarchy_Lock
     */
    public function revalidate()
    {
        if (!$this->isEnabled()) {
            return $this;
        }
        if (!$this->isLocked() || $this->isLockedByMe()) {
            $this->lock();
        }
        return $this;
    }

    /**
     * Check whether lock is actual
     *
     * @return bool
     */
    public function isActual()
    {
        $this->loadLockData();
        if ($this->hasData('started_at') && $this->_getData('started_at') + $this->getLockLifeTime() > time()) {
            return true;
        }
        return false;
    }

    /**
     * Check whether lock functionality is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return ($this->getLockLifeTime() > 0);
    }

    /**
     * Check whether current user is lock owner or not
     *
     * @return bool
     */
    public function isLockOwner()
    {
        $this->loadLockData();
        if ($this->_getData('user_id') == $this->_getSession()->getUser()->getId()
            && $this->_getData('session_id') == $this->_getSession()->getSessionId()
        ) {
            return true;
        }
        return false;
    }

    /**
     * Create lock for page, previously deleting existing lock
     *
     * @return Magento_VersionsCms_Model_Hierarchy_Lock
     */
    public function lock()
    {
        $this->loadLockData();
        if ($this->getId()) {
            $this->delete();
        }

        $this->setData(array(
            'user_id' => $this->_getSession()->getUser()->getId(),
            'user_name' => $this->_getSession()->getUser()->getName(),
            'session_id' => $this->_getSession()->getSessionId(),
            'started_at' => time()
        ));
        $this->save();

        return $this;
    }

    /**
     * Return lock lifetime in seconds
     *
     * @return int
     */
    public function getLockLifeTime()
    {
        $timeout = (int)$this->_coreStoreConfig->getConfig('cms/hierarchy/lock_timeout');
        return ($timeout != 0 && $timeout < 120 ) ? 120 : $timeout;
    }
}
