<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Persistent
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Persistent\Model;

/**
 * Persistent Session Model
 */
class Session extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Persistent cookie key length
     */
    const KEY_LENGTH = 50;

    /**
     * Persistent cookie name
     */
    const COOKIE_NAME = 'persistent_shopping_cart';

    /**
     * Fields which model does not save into `info` db field
     *
     * @var string[]
     */
    protected $_unserializableFields = array(
        'persistent_id',
        'key',
        'customer_id',
        'website_id',
        'info',
        'updated_at'
    );

    /**
     * If model loads expired sessions
     *
     * @var bool
     */
    protected $_loadExpired = false;

    /**
     * Persistent data
     *
     * @var \Magento\Persistent\Helper\Data
     */
    protected $_persistentData;

    /**
     * Core data
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_coreConfig;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Cookie model
     *
     * @var \Magento\Stdlib\Cookie
     */
    protected $_cookie;

    /**
     * @var \Magento\Math\Random
     */
    protected $mathRandom;

    /**
     * @var \Magento\Framework\Session\Config\ConfigInterface
     */
    protected $sessionConfig;

    /**
     * Construct
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $coreConfig
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Persistent\Helper\Data $persistentData
     * @param \Magento\Stdlib\Cookie $cookie
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Math\Random $mathRandom
     * @param \Magento\Framework\Session\Config\ConfigInterface $sessionConfig
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $coreConfig,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Persistent\Helper\Data $persistentData,
        \Magento\Stdlib\Cookie $cookie,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Math\Random $mathRandom,
        \Magento\Framework\Session\Config\ConfigInterface $sessionConfig,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_coreData = $coreData;
        $this->_persistentData = $persistentData;
        $this->_coreConfig = $coreConfig;
        $this->_cookie = $cookie;
        $this->_storeManager = $storeManager;
        $this->sessionConfig = $sessionConfig;
        $this->mathRandom = $mathRandom;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Persistent\Model\Resource\Session');
    }

    /**
     * Set if load expired persistent session
     *
     * @param bool $loadExpired
     * @return $this
     */
    public function setLoadExpired($loadExpired = true)
    {
        $this->_loadExpired = $loadExpired;
        return $this;
    }

    /**
     * Get if model loads expired sessions
     *
     * @return bool
     */
    public function getLoadExpired()
    {
        return $this->_loadExpired;
    }

    /**
     * Get date-time before which persistent session is expired
     *
     * @param int|string|\Magento\Store\Model\Store $store
     * @return string
     */
    public function getExpiredBefore($store = null)
    {
        return gmdate('Y-m-d H:i:s', time() - $this->_persistentData->getLifeTime($store));
    }

    /**
     * Serialize info for Resource Model to save
     * For new model check and set available cookie key
     *
     * @return $this
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        // Setting info
        $info = array();
        foreach ($this->getData() as $index => $value) {
            if (!in_array($index, $this->_unserializableFields)) {
                $info[$index] = $value;
            }
        }
        $this->setInfo($this->_coreData->jsonEncode($info));

        if ($this->isObjectNew()) {
            $this->setWebsiteId($this->_storeManager->getStore()->getWebsiteId());
            // Setting cookie key
            do {
                $this->setKey($this->mathRandom->getRandomString(self::KEY_LENGTH));
            } while (!$this->getResource()->isKeyAllowed($this->getKey()));
        }

        return $this;
    }

    /**
     * Set model data from info field
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        $info = $this->_coreData->jsonDecode($this->getInfo());
        if (is_array($info)) {
            foreach ($info as $key => $value) {
                $this->setData($key, $value);
            }
        }
        return $this;
    }

    /**
     * Get persistent session by cookie key
     *
     * @param string $key
     * @return $this
     */
    public function loadByCookieKey($key = null)
    {
        if (null === $key) {
            $key = $this->_cookie->get(self::COOKIE_NAME);
        }
        if ($key) {
            $this->load($key, 'key');
        }

        return $this;
    }

    /**
     * Load session model by specified customer id
     *
     * @param int $id
     * @return $this
     */
    public function loadByCustomerId($id)
    {
        return $this->load($id, 'customer_id');
    }

    /**
     * Delete customer persistent session by customer id
     *
     * @param int $customerId
     * @param bool $clearCookie
     * @return $this
     */
    public function deleteByCustomerId($customerId, $clearCookie = true)
    {
        if ($clearCookie) {
            $this->removePersistentCookie();
        }
        $this->getResource()->deleteByCustomerId($customerId);
        return $this;
    }

    /**
     * Remove persistent cookie
     *
     * @return $this
     */
    public function removePersistentCookie()
    {
        $this->_cookie->set(self::COOKIE_NAME, null, null, $this->sessionConfig->getCookiePath());
        return $this;
    }

    /**
     * Delete expired persistent sessions for the website
     *
     * @param null|int $websiteId
     * @return $this
     */
    public function deleteExpired($websiteId = null)
    {
        if (is_null($websiteId)) {
            $websiteId = $this->_storeManager->getStore()->getWebsiteId();
        }

        $lifetime = $this->_coreConfig->getValue(
            \Magento\Persistent\Helper\Data::XML_PATH_LIFE_TIME,
            'website',
            intval($websiteId)
        );

        if ($lifetime) {
            $this->getResource()->deleteExpired($websiteId, gmdate('Y-m-d H:i:s', time() - $lifetime));
        }

        return $this;
    }

    /**
     * Delete 'persistent' cookie
     *
     * @return $this
     */
    protected function _afterDeleteCommit()
    {
        $this->removePersistentCookie();
        return parent::_afterDeleteCommit();
    }

    /**
     * Set `updated_at` to be always changed
     *
     * @return $this
     */
    public function save()
    {
        $this->setUpdatedAt(gmdate('Y-m-d H:i:s'));
        return parent::save();
    }
}
