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
    array(
        'NO_CACHE_COOKIE',
        'Magento\FullPageCache\Model\Processor',
        'Magento\FullPageCache\Model\Processor\RestrictionInterface::NO_CACHE_COOKIE'
    ),
    array('XML_PATH_DEFAULT_TIMEZONE', 'Magento\CatalogEvent\Model\Event'),
    array(
        'METADATA_CACHE_SUFFIX',
        'Magento\FullPageCache\Model\Processor',
        'Magento\FullPageCache\Model\MetadataInterface::METADATA_CACHE_SUFFIX'
    ),
    array(
        'REQUEST_ID_PREFIX',
        'Magento\FullPageCache\Model\Processor',
        'Magento\FullPageCache\Model\Request\Identifier::REQUEST_ID_PREFIX'
    ),
    array(
        'DESIGN_EXCEPTION_KEY',
        'Magento\FullPageCache\Model\Processor',
        'Magento\FullPageCache\Model\DesignPackage\Info::DESIGN_EXCEPTION_KEY'
    ),
    array(
        'DESIGN_CHANGE_CACHE_SUFFIX',
        'Magento\FullPageCache\Model\Processor',
        'Magento\FullPageCache\Model\DesignPackage\Rules::DESIGN_CHANGE_CACHE_SUFFIX'
    ),
    array('XML_PATH_ACL_DENY_RULES', 'Magento_AdminGws_Model_Observer'),
    array('XML_PATH_VALIDATE_CALLBACK', 'Magento_AdminGws_Model_Observer'),
    array('XML_CHARSET_NODE', 'Magento_GiftCardAccount_Model_Pool'),
    array('XML_CHARSET_SEPARATOR', 'Magento_GiftCardAccount_Model_Pool'),
    array('XML_PATH_SKIP_GLOBAL_FIELDS', 'Magento_Logging_Model_Event_Changes'),
    array(
        'XML_PATH_RESTRICTION_ENABLED',
        'Magento_WebsiteRestriction_Helper_Data',
        'Magento_WebsiteRestriction_Model_Config::XML_PATH_RESTRICTION_ENABLED'
    ),
    array(
        'XML_PATH_RESTRICTION_MODE',
        'Magento_WebsiteRestriction_Helper_Data',
        'Magento_WebsiteRestriction_Model_Config::XML_PATH_RESTRICTION_MODE'
    ),
    array(
        'XML_PATH_RESTRICTION_LANDING_PAGE',
        'Magento_WebsiteRestriction_Helper_Data',
        'Magento_WebsiteRestriction_Model_Config::XML_PATH_RESTRICTION_LANDING_PAGE'
    ),
    array(
        'XML_PATH_RESTRICTION_HTTP_STATUS',
        'Magento_WebsiteRestriction_Helper_Data',
        'Magento_WebsiteRestriction_Model_Config::XML_PATH_RESTRICTION_HTTP_STATUS'
    ),
    array(
        'XML_PATH_RESTRICTION_HTTP_REDIRECT',
        'Magento_WebsiteRestriction_Helper_Data',
        'Magento_WebsiteRestriction_Model_Config::XML_PATH_RESTRICTION_HTTP_REDIRECT'
    ),
    array('XML_NODE_RESTRICTION_ALLOWED_GENERIC', 'Magento_WebsiteRestriction_Helper_Data'),
    array('XML_NODE_RESTRICTION_ALLOWED_REGISTER', 'Magento_WebsiteRestriction_Helper_Data'),
    array('XML_PATH_CONTEXT_MENU_LAYOUTS', 'Magento\VersionsCms\Model\Hierarchy\Config'),
    array('XML_NODE_ALLOWED_CACHE', 'Magento\FullPageCache\Model\Processor'),
    array(
        'XML_PATH_GRANT_CATALOG_CATEGORY_VIEW',
        'Magento\CatalogPermissions\Helper\Data',
        'Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_GRANT_CATALOG_CATEGORY_VIEW'
    ),
    array(
        'XML_PATH_GRANT_CATALOG_PRODUCT_PRICE',
        'Magento\CatalogPermissions\Helper\Data',
        'Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_GRANT_CATALOG_PRODUCT_PRICE'
    ),
    array(
        'XML_PATH_GRANT_CHECKOUT_ITEMS',
        'Magento\CatalogPermissions\Helper\Data',
        'Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_GRANT_CHECKOUT_ITEMS'
    ),
    array(
        'XML_PATH_DENY_CATALOG_SEARCH',
        'Magento\CatalogPermissions\Helper\Data',
        'Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_DENY_CATALOG_SEARCH'
    ),
    array(
        'XML_PATH_LANDING_PAGE',
        'Magento\CatalogPermissions\Helper\Data',
        'Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_LANDING_PAGE'
    ),
    array(
        'GRANT_ALL',
        'Magento\CatalogPermissions\Helper\Data',
        'Magento\CatalogPermissions\App\ConfigInterface::GRANT_ALL'
    ),
    array(
        'GRANT_CUSTOMER_GROUP',
        'Magento\CatalogPermissions\Helper\Data',
        'Magento\CatalogPermissions\App\ConfigInterface::GRANT_CUSTOMER_GROUP'
    ),
    array(
        'GRANT_NONE',
        'Magento\CatalogPermissions\Helper\Data',
        'Magento\CatalogPermissions\App\ConfigInterface::GRANT_NONE'
    ),
    array(
        'XML_PATH_GRANT_CATALOG_CATEGORY_VIEW',
        'Magento\CatalogPermissions\Model\Observer',
        'Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_GRANT_CATALOG_CATEGORY_VIEW'
    ),
    array(
        'XML_PATH_GRANT_CATALOG_PRODUCT_PRICE',
        'Magento\CatalogPermissions\Model\Observer',
        'Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_GRANT_CATALOG_PRODUCT_PRICE'
    ),
    array(
        'XML_PATH_GRANT_CHECKOUT_ITEMS',
        'Magento\CatalogPermissions\Model\Observer',
        'Magento\CatalogPermissions\App\ConfigInterface::XML_PATH_GRANT_CHECKOUT_ITEMS'
    ),
    array('XML_PATH_GRANT_BASE', 'Magento\CatalogPermissions\Model\Resource\Permission\Index'),
    array('EVENT_TYPE_REINDEX_PRODUCTS', 'Magento\CatalogPermissions\Model\Permission\Index'),
    array('ENTITY_CATEGORY', 'Magento\CatalogPermissions\Model\Permission\Index'),
    array('ENTITY_PRODUCT', 'Magento\CatalogPermissions\Model\Permission\Index'),
    array('ENTITY_CONFIG', 'Magento\CatalogPermissions\Model\Permission\Index'),
    array(
        'FORM_SELECT_ALL_VALUES',
        'Magento\CatalogPermissions\Model\Adminhtml\Observer',
        'Magento\CatalogPermissions\Block\Adminhtml\Catalog\Category\Tab\Permissions\Row::FORM_SELECT_ALL_VALUES'
    ),
    array(
        'STATUS_NEW',
        'Magento\Invitation\Model\Invitation',
        'Magento\Invitation\Model\Invitation\Status::STATUS_NEW',
    ),
    array(
        'STATUS_SENT',
        'Magento\Invitation\Model\Invitation',
        'Magento\Invitation\Model\Invitation\Status::STATUS_SENT',
    ),
    array(
        'STATUS_ACCEPTED',
        'Magento\Invitation\Model\Invitation',
        'Magento\Invitation\Model\Invitation\Status::STATUS_ACCEPTED',
    ),
    array(
        'STATUS_CANCELED',
        'Magento\Invitation\Model\Invitation',
        'Magento\Invitation\Model\Invitation\Status::STATUS_CANCELED',
    ),
    ['XML_PATH_DEFAULT_VALUES', 'Magento\TargetRule\Model\Rule'],
);
