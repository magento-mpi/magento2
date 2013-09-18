<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Wishlist_Model_Config
{
    const XML_PATH_PRODUCT_ATTRIBUTES = 'global/wishlist/item/product_attributes';
    const XML_PATH_SHARING_EMAIL_LIMIT = 'wishlist/email/number_limit';
    const XML_PATH_SHARING_TEXT_LIMIT = 'wishlist/email/text_limit';
    const SHARING_EMAIL_LIMIT = 10;
    const SHARING_TEXT_LIMIT = 255;

    /**
     * Number of emails allowed for sharing wishlist
     *
     * @var int
     */
    protected $_sharingEmailLimit;

    /**
     * Number of symbols in email for sharing
     *
     * @var int
     */
    protected $_sharingTextLimit;

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_coreConfig;

    /**
     * Constructor
     *
     * @param Magento_Core_Model_Store_Config $storeConfig
     * @param Magento_Core_Model_Config $coreConfig
     */
    public function __construct(
        Magento_Core_Model_Store_Config $storeConfig,
        Magento_Core_Model_Config $coreConfig
    ) {
        $emailLimitInConfig = (int) $storeConfig->getConfig(self::XML_PATH_SHARING_EMAIL_LIMIT);
        $textLimitInConfig = (int) $storeConfig->getConfig(self::XML_PATH_SHARING_TEXT_LIMIT);
        $this->_sharingEmailLimit = $emailLimitInConfig ?: self::SHARING_EMAIL_LIMIT;
        $this->_sharignTextLimit = $textLimitInConfig ?: self::SHARING_TEXT_LIMIT;
        $this->_storeConfig = $storeConfig;
        $this->_coreConfig = $coreConfig;
    }

    /**
     * Get product attributes that need in wishlist
     *
     * @return array
     */
    public function getProductAttributes()
    {
        $attrsForCatalog  = Mage::getSingleton('Magento_Catalog_Model_Config')->getProductAttributes();
        $attrsForWishlist = $this->_coreConfig->getNode(self::XML_PATH_PRODUCT_ATTRIBUTES)->asArray();

        return array_merge($attrsForCatalog, array_keys($attrsForWishlist));
    }

    /**
     * Retrieve number of emails allowed for sharing wishlist
     *
     * @return int
     */
    public function getSharingEmailLimit()
    {
        return $this->_sharingEmailLimit;
    }

    /**
     * Retrieve maximum length of sharing email text
     *
     * @return int
     */
    public function getSharingTextLimit()
    {
        return $this->_sharignTextLimit;
    }
}