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
 * @method \Magento\VersionsCms\Model\Resource\Hierarchy\Lock _getResource()
 * @method \Magento\VersionsCms\Model\Resource\Hierarchy\Lock getResource()
 * @method int getUserId()
 * @method \Magento\VersionsCms\Model\Hierarchy\Lock setUserId(int $value)
 * @method string getUserName()
 * @method \Magento\VersionsCms\Model\Hierarchy\Lock setUserName(string $value)
 * @method string getSessionId()
 * @method \Magento\VersionsCms\Model\Hierarchy\Lock setSessionId(string $value)
 * @method int getStartedAt()
 * @method \Magento\VersionsCms\Model\Hierarchy\Lock setStartedAt(int $value)
 *
 * @deprecated since 1.12.0.0
 */
namespace Magento\VersionsCms\Model\Hierarchy;

class Lock extends \Magento\Core\Model\AbstractModel
{
    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * Session model instance
     *
     * @var \Magento\Session\SessionManagerInterface
     */
    protected $_session;

    /**
     * Flag indicating whether lock data loaded or not
     *
     * @var bool
     */
    protected $_dataLoaded = false;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_backendAuthSession;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_backendAuthSession = $backendAuthSession;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Resource model initializing
     */
    protected function _construct()
    {
        $this->_init('Magento\VersionsCms\Model\Resource\Hierarchy\Lock');
    }

    /**
     * Setter for session instance
     *
     * @param \Magento\Session\SessionManagerInterface $session
     * @return \Magento\VersionsCms\Model\Hierarchy\Lock
     */
    public function setSession(\Magento\Session\SessionManagerInterface $session)
    {
        $this->_session = $session;
        return $this;
    }

    /**
     * Getter for session instance
     *
     * @return \Magento\Session\SessionManagerInterface
     */
    protected function _getSession()
    {
        if ($this->_session === null) {
            return $this->_backendAuthSession;
        }
        return $this->_session;
    }

    /**
     * Load lock data
     *
     * @return \Magento\VersionsCms\Model\Hierarchy\Lock
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
     * @return \Magento\VersionsCms\Model\Hierarchy\Lock
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
     * @return \Magento\VersionsCms\Model\Hierarchy\Lock
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
