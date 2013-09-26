<?php
/**
 * Same as obsolete_constants.php, but specific to Magento EE
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    array('LAST_PRODUCT_COOKIE', 'Magento\FullPageCache\Model\Processor'),
    array('NO_CACHE_COOKIE', 'Magento\FullPageCache\Model\Processor',
        'Magento\FullPageCache\Model\Processor\RestrictionInterface::NO_CACHE_COOKIE'
    ),
    array('XML_PATH_DEFAULT_TIMEZONE', 'Magento\CatalogEvent\Model\Event'),
    array('METADATA_CACHE_SUFFIX', 'Magento\FullPageCache\Model\Processor',
        'Magento\FullPageCache\Model\MetadataInterface::METADATA_CACHE_SUFFIX'
    ),
    array('REQUEST_ID_PREFIX', 'Magento\FullPageCache\Model\Processor',
        'Magento\FullPageCache\Model\Request\Identifier::REQUEST_ID_PREFIX'
    ),
    array('DESIGN_EXCEPTION_KEY', 'Magento\FullPageCache\Model\Processor',
        'Magento\FullPageCache\Model\DesignPackage\Info::DESIGN_EXCEPTION_KEY'
    ),
    array('DESIGN_CHANGE_CACHE_SUFFIX', 'Magento\FullPageCache\Model\Processor',
        'Magento\FullPageCache\Model\DesignPackage\Rules::DESIGN_CHANGE_CACHE_SUFFIX'
    ),
    array('XML_PATH_ACL_DENY_RULES', 'Magento_AdminGws_Model_Observer'),
    array('XML_PATH_VALIDATE_CALLBACK', 'Magento_AdminGws_Model_Observer'),
    array('XML_CHARSET_NODE', 'Magento_GiftCardAccount_Model_Pool'),
    array('XML_CHARSET_SEPARATOR', 'Magento_GiftCardAccount_Model_Pool'),
    array('XML_PATH_SKIP_GLOBAL_FIELDS', 'Magento_Logging_Model_Event_Changes'),
    array('XML_PATH_RESTRICTION_ENABLED', 'Magento_WebsiteRestriction_Helper_Data',
        'Magento_WebsiteRestriction_Model_Config::XML_PATH_RESTRICTION_ENABLED'
    ),
    array('XML_PATH_RESTRICTION_MODE', 'Magento_WebsiteRestriction_Helper_Data',
        'Magento_WebsiteRestriction_Model_Config::XML_PATH_RESTRICTION_MODE'
    ),
    array('XML_PATH_RESTRICTION_LANDING_PAGE', 'Magento_WebsiteRestriction_Helper_Data',
        'Magento_WebsiteRestriction_Model_Config::XML_PATH_RESTRICTION_LANDING_PAGE'
    ),
    array('XML_PATH_RESTRICTION_HTTP_STATUS', 'Magento_WebsiteRestriction_Helper_Data',
        'Magento_WebsiteRestriction_Model_Config::XML_PATH_RESTRICTION_HTTP_STATUS'
    ),
    array('XML_PATH_RESTRICTION_HTTP_REDIRECT', 'Magento_WebsiteRestriction_Helper_Data',
        'Magento_WebsiteRestriction_Model_Config::XML_PATH_RESTRICTION_HTTP_REDIRECT'
    ),
    array('XML_NODE_RESTRICTION_ALLOWED_GENERIC', 'Magento_WebsiteRestriction_Helper_Data'),
    array('XML_NODE_RESTRICTION_ALLOWED_REGISTER', 'Magento_WebsiteRestriction_Helper_Data'),
);
