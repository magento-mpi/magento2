<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Persistent
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Persistent Session Model
 *
 * @category   Magento
 * @package    Magento_Persistent
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Persistent_Model_Session extends Magento_Core_Model_Abstract
{
    const KEY_LENGTH = 50;
    const COOKIE_NAME = 'persistent_shopping_cart';

    /**
     * Fields which model does not save into `info` db field
     *
     * @var array
     */
    protected $_unserializableFields = array('persistent_id', 'key', 'customer_id', 'website_id', 'info', 'updated_at');

    /**
     * If model loads expired sessions
     *
     * @var bool
     */
    protected $_loadExpired = false;

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_coreConfig;

    /**
     * Constructor
     *
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Config $coreConfig
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Config $coreConfig,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
        $this->_coreConfig = $coreConfig;
    }

    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('Magento_Persistent_Model_Resource_Session');
    }

    /**
     * Set if load expired persistent session
     *
     * @param bool $loadExpired
     * @return Magento_Persistent_Model_Session
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
     * @param int|string|Magento_Core_Model_Store $store
     * @return string
     */
    public function getExpiredBefore($store = null)
    {
        return gmdate('Y-m-d H:i:s', time() - Mage::helper('Magento_Persistent_Helper_Data')->getLifeTime($store));
    }

    /**
     * Serialize info for Resource Model to save
     * For new model check and set available cookie key
     *
     * @return Magento_Persistent_Model_Session
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
        $this->setInfo(Mage::helper('Magento_Core_Helper_Data')->jsonEncode($info));

        if ($this->isObjectNew()) {
            $this->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
            // Setting cookie key
            do {
                $this->setKey(Mage::helper('Magento_Core_Helper_Data')->getRandomString(self::KEY_LENGTH));
            } while (!$this->getResource()->isKeyAllowed($this->getKey()));
        }

        return $this;
    }

    /**
     * Set model data from info field
     *
     * @return Magento_Persistent_Model_Session
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        $info = Mage::helper('Magento_Core_Helper_Data')->jsonDecode($this->getInfo());
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
     * @return Magento_Persistent_Model_Session
     */
    public function loadByCookieKey($key = null)
    {
        if (is_null($key)) {
            $key = Mage::getSingleton('Magento_Core_Model_Cookie')->get(Magento_Persistent_Model_Session::COOKIE_NAME);
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
     * @return Magento_Core_Model_Abstract
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
     * @return Magento_Persistent_Model_Session
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
     * @return Magento_Persistent_Model_Session
     */
    public function removePersistentCookie()
    {
        Mage::getSingleton('Magento_Core_Model_Cookie')->delete(Magento_Persistent_Model_Session::COOKIE_NAME);
        return $this;
    }

    /**
     * Delete expired persistent sessions for the website
     *
     * @param null|int $websiteId
     * @return Magento_Persistent_Model_Session
     */
    public function deleteExpired($websiteId = null)
    {
        if (is_null($websiteId)) {
            $websiteId = Mage::app()->getStore()->getWebsiteId();
        }

        $lifetime = $this->_coreConfig->getValue(
            Magento_Persistent_Helper_Data::XML_PATH_LIFE_TIME,
            'website',
            intval($websiteId)
        );

        if ($lifetime) {
            $this->getResource()->deleteExpired(
                $websiteId,
                gmdate('Y-m-d H:i:s', time() - $lifetime)
            );
        }

        return $this;
    }

    /**
     * Delete 'persistent' cookie
     *
     * @return Magento_Core_Model_Abstract
     */
    protected function _afterDeleteCommit() {
        Mage::getSingleton('Magento_Core_Model_Cookie')->delete(Magento_Persistent_Model_Session::COOKIE_NAME);
        return parent::_afterDeleteCommit();
    }

    /**
     * Set `updated_at` to be always changed
     *
     * @return Magento_Persistent_Model_Session
     */
    public function save()
    {
        $this->setUpdatedAt(gmdate('Y-m-d H:i:s'));
        return parent::save();
    }
}
