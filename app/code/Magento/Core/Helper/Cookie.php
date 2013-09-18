<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Core Cookie helper
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Core_Helper_Cookie extends Magento_Core_Helper_Abstract
{
    /**
     * Cookie name for users who allowed cookie save
     */
    const IS_USER_ALLOWED_SAVE_COOKIE  = 'user_allowed_save_cookie';

    /**
     * Path to configuration, check is enable cookie restriction mode
     */
    const XML_PATH_COOKIE_RESTRICTION  = 'web/cookie/cookie_restriction';

    /**
     * Cookie restriction lifetime configuration path
     */
    const XML_PATH_COOKIE_RESTRICTION_LIFETIME = 'web/cookie/cookie_restriction_lifetime';

    /**
     * @var Magento_Core_Model_Store
     */
    protected $_currentStore;

    /**
     * @var Magento_Core_Model_Cookie
     */
    protected $_cookieModel;

    /**
     * @var Magento_Core_Model_Website
     */
    protected $_website;

    /**
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Model_Cookie $cookie
     * @param array $data
     * @throws InvalidArgumentException
     */
    public function __construct(
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_Cookie $cookie,
        array $data = array())
    {
        parent::__construct($context);
        $this->_currentStore = isset($data['current_store']) ? $data['current_store'] : $storeManager->getStore();

        if (!($this->_currentStore instanceof Magento_Core_Model_Store)) {
            throw new InvalidArgumentException('Required store object is invalid');
        }

        $this->_cookieModel = isset($data['cookie_model'])
            ? $data['cookie_model'] : $cookie;

        if (false == ($this->_cookieModel instanceof Magento_Core_Model_Cookie)) {
            throw new InvalidArgumentException('Required cookie object is invalid');
        }

        $this->_website = isset($data['website']) ? $data['website'] : $storeManager->getWebsite();

        if (false == ($this->_website instanceof Magento_Core_Model_Website)) {
            throw new InvalidArgumentException('Required website object is invalid');
        }
    }


    /**
     * Check if cookie restriction notice should be displayed
     *
     * @return bool
     */
    public function isUserNotAllowSaveCookie()
    {
        $acceptedSaveCookiesWebsites = $this->_getAcceptedSaveCookiesWebsites();
        return $this->_currentStore->getConfig(self::XML_PATH_COOKIE_RESTRICTION) &&
            empty($acceptedSaveCookiesWebsites[$this->_website->getId()]);
    }

    /**
     * Return serialized list of accepted save cookie website
     *
     * @return string
     */
    public function getAcceptedSaveCookiesWebsiteIds()
    {
        $acceptedSaveCookiesWebsites = $this->_getAcceptedSaveCookiesWebsites();
        $acceptedSaveCookiesWebsites[$this->_website->getId()] = 1;
        return json_encode($acceptedSaveCookiesWebsites);
    }

    /**
     * Get accepted save cookies websites
     *
     * @return array
     */
    protected function _getAcceptedSaveCookiesWebsites()
    {
        $serializedList = $this->_cookieModel->get(self::IS_USER_ALLOWED_SAVE_COOKIE);
        $unSerializedList = json_decode($serializedList, true);
        return is_array($unSerializedList) ? $unSerializedList : array();
    }

    /**
     * Get cookie restriction lifetime (in seconds)
     *
     * @return int
     */
    public function getCookieRestrictionLifetime()
    {
        return (int)$this->_currentStore->getConfig(self::XML_PATH_COOKIE_RESTRICTION_LIFETIME);
    }
}
