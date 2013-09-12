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
    const XML_PATH_SHARING_EMAIL_LIMIT = 'wishlist/email/number_limit';
    const XML_PATH_SHARING_TEXT_LIMIT = 'wishlist/email/text_limit';
    const SHARING_EMAIL_LIMIT = 10;
    const SHARING_TEXT_LIMIT = 255;

    /**
     * @var Magento_Catalog_Model_Config
     */
    private $_catalogConfig;

    /**
     * @var Magento_Catalog_Model_Attribute_Config
     */
    private $_attributeConfig;

    /**
     * Number of emails allowed for sharing wishlist
     *
     * @var int
     */
    private $_sharingEmailLimit;

    /**
     * Number of symbols in email for sharing
     *
     * @var int
     */
    private $_sharingTextLimit;

    /**
     * @param Magento_Core_Model_Store_Config $storeConfig
     * @param Magento_Catalog_Model_Config $catalogConfig
     * @param Magento_Catalog_Model_Attribute_Config $attributeConfig
     */
    public function __construct(
        Magento_Core_Model_Store_Config $storeConfig,
        Magento_Catalog_Model_Config $catalogConfig,
        Magento_Catalog_Model_Attribute_Config $attributeConfig
    ) {
        $emailLimitInConfig = (int)$storeConfig->getConfig(self::XML_PATH_SHARING_EMAIL_LIMIT);
        $textLimitInConfig = (int)$storeConfig->getConfig(self::XML_PATH_SHARING_TEXT_LIMIT);
        $this->_sharingEmailLimit = $emailLimitInConfig ?: self::SHARING_EMAIL_LIMIT;
        $this->_sharignTextLimit = $textLimitInConfig ?: self::SHARING_TEXT_LIMIT;
        $this->_catalogConfig = $catalogConfig;
        $this->_attributeConfig = $attributeConfig;
    }

    /**
     * Get product attributes that need in wishlist
     *
     * @return array
     */
    public function getProductAttributes()
    {
        $catalogAttributes  = $this->_catalogConfig->getProductAttributes();
        $wishlistAttributes = $this->_attributeConfig->getAttributeNames('wishlist_item');
        return array_merge($catalogAttributes, $wishlistAttributes);
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
